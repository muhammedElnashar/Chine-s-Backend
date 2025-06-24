@php use Illuminate\Support\Facades\Storage; @endphp
@extends("layouts.app")

@section('title', 'Edit Exam')

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="app-toolbar p-3">
                <div class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-dark fw-bolder fs-2">Edit Section Exam</h1>
                    </div>
                    <div>
                        <a href="{{ route('courses.levels.index', [$course, $level]) }}" class="btn btn-light" style="border-radius: 20px;">Back to Level</a>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form method="POST" action="{{ route('courses.levels.exams.update', [$course, $level, $exam]) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-7">
                                    <label class="form-label required">Exam Title</label>
                                    <input type="text" name="title" class="form-control form-control-solid @error('title') is-invalid @enderror" value="{{ old('title', $exam->title) }}" required>
                                    @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-7">
                                    <label class="form-label">Exam Description</label>
                                    <textarea name="description" class="form-control form-control-solid @error('description') is-invalid @enderror">{{ old('description', $exam->description) }}</textarea>
                                    @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <hr>
                                <h3>Questions</h3>

                                <div id="questions-container">
                                    @foreach($exam->questions as $qIndex => $question)
                                        @php
                                            $types = ['text' => 'Text', 'image' => 'Image', 'video' => 'Video', 'audio' => 'Audio'];
                                            $currentType = old("questions.$qIndex.type", $question->question_type ?? 'text');
                                            $fileUrl = $question->question_media_url ? Storage::disk('s3')->url($question->question_media_url) : null;
                                        @endphp

                                        <div class="question-block border rounded p-4 mb-7 position-relative">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">
                                                Delete
                                            </button>

                                            <h4 class="mb-3">Question <span class="question-number">{{ $qIndex + 1 }}</span></h4>

                                            <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">

                                            <div class="mb-4">
                                                <label class="form-label required">Question Type</label>
                                                <select name="questions[{{ $qIndex }}][question_type]" class="form-select question-type-select" data-index="{{ $qIndex }}" required>
                                                    @foreach($types as $key => $label)
                                                        <option value="{{ $key }}" {{ $currentType === $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-4 question-text-input" data-index="{{ $qIndex }}">
                                                <label class="form-label required">Question Text</label>
                                                <input type="text" name="questions[{{ $qIndex }}][question_text]" class="form-control" value="{{ old("questions.$qIndex.question_text", $question->question_text) }}">
                                            </div>

                                            <div class="mb-4 question-file-upload" data-index="{{ $qIndex }}" style="{{ $currentType === 'text' ? 'display:none;' : '' }}">
                                                <label class="form-label required">Upload Question File</label>
                                                <input type="file" name="questions[{{ $qIndex }}][question_media]" class="form-control" />
                                                @if($fileUrl)
                                                    @if($currentType === 'image')
                                                        <img src="{{ $fileUrl }}" alt="Question Image" style="max-width: 50px; margin-top: 10px;">
                                                    @elseif($currentType === 'video')
                                                        <video controls style="max-width: 50px; margin-top: 10px;">
                                                            <source src="{{ $fileUrl }}" type="video/mp4">
                                                        </video>
                                                    @elseif($currentType === 'audio')
                                                        <audio controls style="margin-top: 10px;">
                                                            <source src="{{ $fileUrl }}" type="audio/mpeg">
                                                        </audio>
                                                    @endif
                                                @endif
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label">Explanation (Optional)</label>
                                                <textarea name="questions[{{ $qIndex }}][explanation]" class="form-control" rows="2">{{ old("questions.$qIndex.explanation", $question->explanation ?? '') }}</textarea>
                                            </div>

                                            <h5>Answers</h5>
                                            @foreach($question->answers as $aIndex => $answer)
                                                <div class="input-group input-group-solid mb-3">
                                                    <input type="text" name="questions[{{ $qIndex }}][answers][]" class="form-control"
                                                           placeholder="Answer {{ $aIndex + 1 }}"
                                                           value="{{ old("questions.$qIndex.answers.$aIndex", $answer->answer_text) }}" required>

                                                    <div class="input-group-text">
                                                        <input type="radio" name="questions[{{ $qIndex }}][correct_answer]" value="{{ $aIndex }}"
                                                               {{ $answer->is_correct ? 'checked' : '' }} required>
                                                        <span class="ms-2">Correct</span>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-5">
                                    <button type="button" class="btn btn-secondary" id="add-question-btn">Add Question</button>
                                </div>

                                <div class="text-center pt-10">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Update Exam</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        let questionIndex = {{ $exam->questions->count() }};
        const questionTypes = ['text', 'image', 'video', 'audio'];

        function generateQuestionHtml(index) {
            return `
        <div class="question-block border rounded p-4 mb-7 position-relative">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">Delete</button>
            <h4 class="mb-3">Question <span class="question-number">${index + 1}</span></h4>



            <div class="mb-4">
                <label class="form-label required">Question Type</label>
                <select name="questions[${index}][question_type]" class="form-select question-type-select" data-index="${index}" required>
                    ${questionTypes.map(type => `<option value="${type}">${type.charAt(0).toUpperCase() + type.slice(1)}</option>`).join('')}
                </select>
            </div>
            <div class="mb-4 question-text-input" data-index="${index}">
                <label class="form-label  required">Question Text</label>
                <input type="text" name="questions[${index}][question_text]" class="form-control" required>
            </div>

            <div class="mb-4 question-file-upload" style="display:none;" data-index="${index}">
                <label class="form-label">Upload Question File</label>
                <input type="file" name="questions[${index}][question_media]" class="form-control" />
            </div>


            <h5>Answers</h5>
            ${[0, 1, 2, 3].map(i => `
                <div class="input-group input-group-solid mb-3">
                    <input type="text" name="questions[${index}][answers][]" class="form-control" placeholder="Answer ${i + 1}" required>
                    <div class="input-group-text">
                        <input type="radio" name="questions[${index}][correct_answer]" value="${i}" required>
                        <span class="ms-2">Correct</span>
                    </div>
                </div>
            `).join('')}

            <div class="mb-4">
                <label class="form-label">Explanation (Optional)</label>
                <textarea name="questions[${index}][explanation]" class="form-control" rows="2"></textarea>
            </div>

        </div>`;
        }

        $('#add-question-btn').on('click', function () {
            $('#questions-container').append(generateQuestionHtml(questionIndex));
            questionIndex++;
            updateQuestionNumbers();
        });

        $(document).on('click', '.remove-question-btn', function () {
            $(this).closest('.question-block').remove();
            updateQuestionNumbers();
        });

        function updateQuestionNumbers() {
            $('.question-block').each(function(index) {
                $(this).find('.question-number').text(index + 1);
            });
        }

        // Toggle file upload field visibility based on question type
        $(document).on('change', '.question-type-select', function () {
            const index = $(this).data('index');
            const selectedType = $(this).val();

            const fileUploadDiv = $('.question-file-upload[data-index="'+index+'"]');
            const textInputDiv = $('.question-text-input[data-index="' + index + '"]');
            const textInput = textInputDiv.find('input');
            const fileInput = fileUploadDiv.find('input[type="file"]');

            if (selectedType === 'text') {
                fileUploadDiv.hide();
                fileInput.prop('required', false).val('');
                textInput.prop('required', true);
            } else {
                fileUploadDiv.show();
                fileInput.prop('required', true);
                textInput.prop('required', false);
            }
        });

    </script>
@endpush
