@extends('layouts.app')

@section('title', 'Add New Videos for Level: ' . $level->title)

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Add New Videos for Level: {{ $level->title }}</h3>
                    <a href="{{ route('courses.levels.videos.index', [$course, $level]) }}" class="btn btn-light"
                       style="border-radius: 20px;">Back to Videos</a>
                </div>

                <div class="card-body pt-0">
                    <form id="videos-form" action="{{ route('courses.levels.videos.store', [$course, $level]) }}"
                          method="POST">
                        @csrf
                        <div id="videos-container">
                            <div class="video-item mb-6" data-index="0">
                                <label class="form-label required">Title</label>
                                <input type="text" name="videos[0][title]" class="form-control video-title"
                                       placeholder="Enter video title" required>

                                <label class="form-label mt-3">Upload Video</label>
                                <input type="file" name="videos[0][file]" class="form-control video-file"
                                       accept="video/*" required>

                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="upload-status text-muted"></small>
                                    <div class="video-duration-display"></div>
                                </div>

                                <input type="hidden" name="videos[0][path]" class="video-path">
                                <input type="hidden" name="videos[0][duration]" class="video-duration">

                                <div class="d-flex mt-2">
                                    <button type="button" class="btn btn-warning resume-upload-btn d-none me-2">Resume
                                        Upload
                                    </button>
                                    <button type="button" class="btn btn-danger cancel-upload-btn d-none">Cancel
                                        Upload
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-video-btn" class="btn btn-secondary mb-4">Add Another Video
                        </button>
                        <br>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" id="submit-btn" class="btn btn-primary" disabled>Upload Videos
                            </button>
                            <span id="upload-status-summary" class="text-muted"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

        $(document).ready(function () {
            let videoIndex = 1;
            const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB
            let activeUploads = 0;
            let completedUploads = 0;
            let failedUploads = 0;
            const MAX_RETRY_ATTEMPTS = 3; // Maximum number of automatic retries
            const UPLOAD_TIMEOUT = 60000; // Increase timeout to 60 seconds

            $('#add-video-btn').on('click', function () {
                addVideoItem(videoIndex++);
            });

            $('.video-file').on('change', handleFileSelect);

            $('#videos-form').on('submit', function (e) {
                if (activeUploads > 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Uploads in progress',
                        text: 'Please wait for all uploads to complete before submitting.',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return false;
                }

                // Check if all videos have been uploaded
                const videoItems = $('.video-item');
                let allUploaded = true;

                videoItems.each(function () {
                    const pathValue = $(this).find('.video-path').val();
                    if (!pathValue) {
                        allUploaded = false;
                        return false;
                    }
                });

                if (!allUploaded) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Missing uploads',
                        text: 'Please upload all videos before submitting.',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                    return false;
                }
            });

            function addVideoItem(index) {
                const html = `
        <div class="video-item mb-6" data-index="${index}">
            <label class="form-label required">Title</label>
            <input type="text" name="videos[${index}][title]" class="form-control video-title" placeholder="Enter video title" required>

            <label class="form-label mt-3">Upload Video</label>
            <input type="file" name="videos[${index}][file]" class="form-control video-file" accept="video/*" required>

            <div class="progress mt-2" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: 0%">0%</div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="upload-status text-muted"></small>
                <div class="video-duration-display"></div>
            </div>

            <input type="hidden" name="videos[${index}][path]" class="video-path">
            <input type="hidden" name="videos[${index}][duration]" class="video-duration">

            <div class="d-flex mt-2">
                <button type="button" class="btn btn-warning resume-upload-btn d-none me-2">Resume Upload</button>
                <button type="button" class="btn btn-danger cancel-upload-btn d-none">Cancel Upload</button>
                <button type="button" class="btn btn-danger ms-auto remove-video-btn">Remove</button>
            </div>
        </div>
    `;
                $('#videos-container').append(html);
                $(`[name="videos[${index}][file]"]`).on('change', handleFileSelect);
                $(`[data-index="${index}"] .remove-video-btn`).on('click', function () {
                    const container = $(this).closest('.video-item');
                    const uploadInProgress = container.data('upload-in-progress');

                    if (uploadInProgress) {
                        Swal.fire({
                            title: 'Cancel upload?',
                            text: 'An upload is in progress. Do you want to cancel it?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, cancel upload',
                            cancelButtonText: 'No, keep uploading'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cancelUpload(container);
                                container.remove();
                                updateUploadSummary();
                                checkSubmitButton();
                            }
                        });
                    } else {
                        container.remove();
                        updateUploadSummary();
                        checkSubmitButton();
                    }
                });
            }

            function checkSubmitButton() {
                const allVideos = $('.video-item').length;
                const completedVideos = $('.video-path').filter(function () {
                    return $(this).val().length > 0;
                }).length;

                $('#submit-btn').prop('disabled', completedVideos === 0 || completedVideos < allVideos || activeUploads > 0);

                updateUploadSummary();
            }

            function updateUploadSummary() {
                const total = $('.video-item').length;
                const completed = $('.video-path').filter(function () {
                    return $(this).val().length > 0;
                }).length;
                const inProgress = activeUploads;
                const failed = failedUploads;

                $('#upload-status-summary').html(`
            <span class="badge bg-primary me-2">Total: ${total}</span>
            <span class="badge bg-success me-2">Completed: ${completed}</span>
            <span class="badge bg-info me-2">In Progress: ${inProgress}</span>
            <span class="badge bg-danger">Failed: ${failed}</span>
        `);
            }

            function getVideoDuration(file) {
                return new Promise((resolve) => {
                    const video = document.createElement('video');
                    video.preload = 'metadata';

                    video.onloadedmetadata = function () {
                        window.URL.revokeObjectURL(video.src);
                        const duration = video.duration;
                        resolve(duration);
                    };

                    video.onerror = function () {
                        window.URL.revokeObjectURL(video.src);
                        console.warn('Could not determine video duration');
                        resolve(0); // Return 0 if duration can't be determined
                    };

                    video.src = URL.createObjectURL(file);
                });
            }

            function formatDuration(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                const decimalMinutes = minutes + (remainingSeconds / 60);
                return Number(decimalMinutes.toFixed(2));
            }


            function cancelUpload(container) {
                const uploadId = container.data('upload-id');
                const key = container.data('upload-key');

                if (uploadId && key) {
                    fetch(`{{ route('s3.multipart-abort') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({uploadId, key})
                    }).then(() => {
                        container.find('.upload-status').text('Upload cancelled');
                        container.find('.progress-bar').css('width', '0%').text('0%').removeClass('bg-info bg-warning bg-success').addClass('bg-danger');
                        container.data('upload-in-progress', false);
                        container.find('.cancel-upload-btn').addClass('d-none');
                        container.find('.resume-upload-btn').addClass('d-none');

                        // Reset file input to clear the file
                        container.find('.video-file').val('');
                        container.find('.video-duration-display').text('');
                        container.find('.video-duration').val('');
                        container.find('.video-path').val('');

                        activeUploads--;
                        updateUploadSummary();
                        checkSubmitButton();
                    }).catch(err => {
                        console.error('Error cancelling upload:', err);
                        // Even if abort fails, reset the UI
                        container.data('upload-in-progress', false);
                        activeUploads--;
                        updateUploadSummary();
                        checkSubmitButton();
                    });
                }
            }

            async function handleFileSelect() {
                const container = $(this).closest('.video-item');
                const file = this.files[0];
                if (!file) return;

                // Check if this container already has a completed upload
                if (container.data('upload-complete')) {
                    // Reset the completed state if selecting a new file
                    container.data('upload-complete', false);
                }

                // Check file size and alert if too large
                const fileSizeMB = file.size / (1024 * 1024);
                const MAX_FILE_SIZE_MB = 500; // Set appropriate limit

                if (fileSizeMB > MAX_FILE_SIZE_MB) {
                    Swal.fire({
                        title: 'File too large',
                        html: `The selected file is ${fileSizeMB.toFixed(1)}MB. This may cause upload issues.<br>Continue anyway?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, continue',
                        cancelButtonText: 'No, select another file'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            $(this).val('');
                            return;
                        } else {
                            initializeUpload(container, file);
                        }
                    });
                } else {
                    initializeUpload(container, file);
                }
            }

            async function initializeUpload(container, file) {
                const pathInput = container.find('.video-path');
                const durationInput = container.find('.video-duration');
                const durationDisplay = container.find('.video-duration-display');
                const progressBar = container.find('.progress-bar');
                const status = container.find('.upload-status');
                const resumeBtn = container.find('.resume-upload-btn');
                const cancelBtn = container.find('.cancel-upload-btn');
                const submitBtn = $('#submit-btn');

                // Reset previous upload state
                container.data('upload-in-progress', false);
                container.data('upload-complete', false); // Reset complete flag
                container.removeData('upload-id');
                container.removeData('upload-key');
                container.data('retry-count', 0);
                pathInput.val('');
                durationInput.val('');
                submitBtn.prop('disabled', true);
                status.text('');
                resumeBtn.addClass('d-none');
                cancelBtn.addClass('d-none');

                // Get video duration
                try {
                    status.text('Analyzing video...');
                    const duration = await getVideoDuration(file);
                    const formattedDuration = formatDuration(duration);
                    durationInput.val(formattedDuration);
                    durationDisplay.text(`Duration: ${formattedDuration}`);
                } catch (err) {
                    console.error('Error getting video duration:', err);
                    durationDisplay.text('Unable to get duration');
                }

                const totalParts = Math.ceil(file.size / CHUNK_SIZE);
                status.text('Preparing upload...');

                let uploadId, key, presignedParts;
                try {
                    const presignRes = await fetch(`{{ route('s3.multipart-urls') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            filename: file.name,
                            filetype: file.type,
                            parts: totalParts
                        })
                    });

                    if (!presignRes.ok) {
                        throw new Error(`Server error: ${presignRes.status}`);
                    }

                    const data = await presignRes.json();
                    uploadId = data.uploadId;
                    key = data.key;
                    presignedParts = data.parts;

                    container.data('upload-id', uploadId);
                    container.data('upload-key', key);
                    container.data('total-parts', totalParts);
                } catch (err) {
                    status.text(`Error preparing upload: ${err.message}`);
                    return;
                }

                let uploadedParts = [];
                container.data('uploaded-parts', JSON.stringify(uploadedParts));
                let currentPart = 0;
                container.data('current-part', currentPart);
                let startTime = Date.now();
                let uploadCancelled = false;

                activeUploads++;
                container.data('upload-in-progress', true);
                updateUploadSummary();

                cancelBtn.removeClass('d-none').on('click', function () {
                    Swal.fire({
                        title: 'Cancel upload?',
                        text: 'Are you sure you want to cancel this upload?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, cancel upload',
                        cancelButtonText: 'No, continue uploading'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            uploadCancelled = true;
                            cancelUpload(container);
                        }
                    });
                });

                // Store file in container data for resume functionality
                container.data('file', file);

                // Start the upload process
                await uploadAllChunks(container, file, uploadId, key, presignedParts, currentPart, uploadedParts);
            }

            async function uploadChunk(container, file, partNumber, presignedParts) {
                if (container.data('upload-cancelled')) {
                    throw new Error('Upload cancelled');
                }

                const start = (partNumber - 1) * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const chunk = file.slice(start, end);
                const url = presignedParts.find(p => p.partNumber === partNumber).url;

                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), UPLOAD_TIMEOUT);

                    // Create a more detailed status message
                    container.find('.upload-status').text(`Uploading part ${partNumber}/${container.data('total-parts')} (${(end - start) / (1024 * 1024)}MB chunk)`);

                    const res = await fetch(url, {
                        method: 'PUT',
                        body: chunk,
                        signal: controller.signal
                    });

                    clearTimeout(timeoutId);

                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);

                    const eTag = res.headers.get('ETag');
                    return {PartNumber: partNumber, ETag: eTag};
                } catch (err) {
                    container.data('retry-count', (container.data('retry-count') || 0) + 1);

                    if (err.name === 'AbortError') {
                        throw new Error(`Network timeout uploading part ${partNumber}`);
                    }
                    console.error(`Error uploading part ${partNumber}:`, err);
                    throw err;
                }
            }

            async function uploadAllChunks(container, file, uploadId, key, presignedParts, startPart = 0, uploadedParts = []) {
                const progressBar = container.find('.progress-bar');
                const status = container.find('.upload-status');
                const resumeBtn = container.find('.resume-upload-btn');
                const cancelBtn = container.find('.cancel-upload-btn');
                const pathInput = container.find('.video-path');
                const totalParts = container.data('total-parts') || Math.ceil(file.size / CHUNK_SIZE);
                let currentPart = startPart;
                let startTime = Date.now();

                // Check if this upload is already complete to prevent re-uploading
                if (container.data('upload-complete')) {
                    status.text('Upload already complete');
                    return;
                }

                // If we have existing uploaded parts from previous attempts, use them
                if (container.data('uploaded-parts')) {
                    try {
                        uploadedParts = JSON.parse(container.data('uploaded-parts')) || [];
                    } catch (e) {
                        uploadedParts = [];
                    }
                }

                try {
                    progressBar.removeClass('bg-danger').addClass('bg-info');

                    for (let i = currentPart; i < totalParts; i++) {
                        if (container.data('upload-cancelled')) {
                            throw new Error('Upload cancelled');
                        }

                        try {
                            const part = await uploadChunk(container, file, i + 1, presignedParts);
                            uploadedParts.push(part);
                            currentPart = i + 1;

                            // Save progress in case we need to resume
                            container.data('current-part', currentPart);
                            container.data('uploaded-parts', JSON.stringify(uploadedParts));

                            const progress = Math.round((currentPart / totalParts) * 100);
                            const elapsed = (Date.now() - startTime) / 1000;
                            const mbUploaded = (currentPart * CHUNK_SIZE) / (1024 * 1024);
                            const speed = mbUploaded / elapsed;

                            progressBar.css('width', progress + '%').text(`${progress}%`);

                            // Show ETA if we can calculate it
                            if (speed > 0) {
                                const mbRemaining = ((totalParts - currentPart) * CHUNK_SIZE) / (1024 * 1024);
                                const etaSeconds = mbRemaining / speed;
                                const etaText = etaSeconds > 60
                                    ? `${Math.round(etaSeconds / 60)} min remaining`
                                    : `${Math.round(etaSeconds)} sec remaining`;

                                status.text(`Uploaded ${progress}% (${speed.toFixed(1)} MB/s) - ${etaText}`);
                            } else {
                                status.text(`Uploaded ${progress}% (${speed.toFixed(1)} MB/s)`);
                            }
                        } catch (err) {
                            const retryCount = container.data('retry-count') || 0;

                            if (retryCount < MAX_RETRY_ATTEMPTS) {
                                // Auto-retry with backoff
                                const backoffMs = Math.pow(2, retryCount) * 1000;
                                status.text(`Upload error: ${err.message}. Retrying in ${backoffMs / 1000}s...`);
                                progressBar.removeClass('bg-info').addClass('bg-warning');

                                await new Promise(resolve => setTimeout(resolve, backoffMs));
                                // Retry the same chunk (don't increment i)
                                i--;
                            } else {
                                throw err; // Give up after MAX_RETRY_ATTEMPTS
                            }
                        }
                    }

                    // All parts uploaded, complete the multipart upload
                    status.text('Finalizing upload...');

                    const completeRes = await fetch(`{{ route('s3.multipart-complete') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({uploadId, key, parts: uploadedParts})
                    });

                    if (!completeRes.ok) {
                        throw new Error(`Failed to finalize upload: ${completeRes.status}`);
                    }

                    const completeData = await completeRes.json();

                    pathInput.val(key);
                    status.text('Upload complete');
                    progressBar.removeClass('bg-info bg-warning').addClass('bg-success');
                    cancelBtn.addClass('d-none');
                    resumeBtn.addClass('d-none');

                    // Mark upload as complete and not in progress
                    container.data('upload-in-progress', false);
                    container.data('upload-complete', true); // Add flag to prevent re-upload
                    activeUploads--;
                    completedUploads++;

                    // Clear saved data
                    container.removeData('file');
                    container.removeData('current-part');
                    container.removeData('uploaded-parts');
                    container.removeData('retry-count');

                    updateUploadSummary();
                    checkSubmitButton();
                } catch (e) {
                    if (e.message === 'Upload cancelled') {
                        return;
                    }

                    container.data('retry-count', (container.data('retry-count') || 0) + 1);

                    // Only show resume button if it's a network error, not a cancellation
                    if (e.name === 'TypeError' || e.message.includes('network') || e.message.includes('timeout') || e.message.includes('fetch')) {
                        status.text(`Upload paused: ${e.message}. Click Resume to continue.`);
                        progressBar.removeClass('bg-info bg-success').addClass('bg-warning');
                        resumeBtn.removeClass('d-none').off('click').on('click', function () {
                            resumeBtn.addClass('d-none');
                            // Reset retry count when manually resuming
                            container.data('retry-count', 0);

                            // Resume from where we left off
                            const currentPart = container.data('current-part') || 0;
                            const savedParts = container.data('uploaded-parts') ? JSON.parse(container.data('uploaded-parts')) : [];
                            const file = container.data('file');
                            const uploadId = container.data('upload-id');
                            const key = container.data('upload-key');

                            if (file && uploadId && key) {
                                uploadAllChunks(container, file, uploadId, key, presignedParts, currentPart, savedParts);
                            } else {
                                status.text('Cannot resume - missing upload data');
                            }
                        });
                    } else {
                        status.text('Upload failed: ' + e.message);
                        progressBar.removeClass('bg-info bg-success bg-warning').addClass('bg-danger');
                        failedUploads++;
                        activeUploads--;
                        updateUploadSummary();

                        // Also show resume option for other errors
                        resumeBtn.removeClass('d-none').off('click').on('click', function () {
                            resumeBtn.addClass('d-none');
                            // Reset retry count when manually resuming
                            container.data('retry-count', 0);

                            // Resume from where we left off
                            const currentPart = container.data('current-part') || 0;
                            const savedParts = container.data('uploaded-parts') ? JSON.parse(container.data('uploaded-parts')) : [];
                            const file = container.data('file');
                            const uploadId = container.data('upload-id');
                            const key = container.data('upload-key');

                            if (file && uploadId && key) {
                                uploadAllChunks(container, file, uploadId, key, presignedParts, currentPart, savedParts);
                            } else {
                                status.text('Cannot resume - missing upload data');
                            }
                        });
                    }
                }
            }

            // Check for connection status
            window.addEventListener('online', function () {
                // Auto-resume paused uploads when connection comes back
                $('.video-item').each(function () {
                    const container = $(this);
                    if (container.data('upload-in-progress') && !container.data('upload-cancelled') && !container.data('upload-complete')) {
                        const resumeBtn = container.find('.resume-upload-btn');
                        if (!resumeBtn.hasClass('d-none')) {
                            container.find('.upload-status').text('Network reconnected. Resuming upload...');
                            setTimeout(function () {
                                resumeBtn.trigger('click');
                            }, 1000);
                        }
                    }
                });
            });

            // Initialize summary
            updateUploadSummary();
        });    </script>
@endpush
