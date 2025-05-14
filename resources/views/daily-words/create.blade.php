@extends("layouts.app")

@section('title')
    Add Daily Audio Exercise
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar p-3">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                            Add Daily Audio Exercise
                        </h1>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form id="daily-audio-form" class="form" method="POST" action="{{ route('words.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="required">Exercise Date</span>
                                    </label>
                                    <input type="date" name="exercise_date" class="form-control" required>
                                    @error('exercise_date')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div id="words-container">
                                    @for ($i = 0; $i < 2; $i++)
                                        <div class="word-block border rounded p-4 mb-7 position-relative">
                                            <h4 class="mb-3">Word <span class="word-number">{{ $i + 1 }}</span></h4>

                                            <div class="fv-row mb-4">
                                                <label class="form-label required">Audio File</label>
                                                <input type="file" name="words[{{ $i }}][audio]" class="form-control" accept=".mp3,.wav" required>
                                                @error("words.$i.audio")
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="fv-row mb-4">
                                                <label class="form-label required">Meaning</label>
                                                <input type="text" name="words[{{ $i }}][meaning]" class="form-control" required>
                                                @error("words.$i.meaning")
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                <div class="text-start mb-5">
                                    <button type="button" class="btn btn-secondary" id="add-word-btn">Add Another Word</button>
                                </div>

                                <div class="text-center pt-10">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Create</span>
                                        <span class="indicator-progress">جاري الحفظ...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                    </button>
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let wordIndex = 2;

        $('#add-word-btn').on('click', function () {
            const wordHtml = `
            <div class="word-block border rounded p-4 mb-7 position-relative">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-word-btn">
                    Delete
                </button>

                <h4 class="mb-3">Word <span class="word-number">${wordIndex + 1}</span></h4>

                <div class="fv-row mb-4">
                    <label class="form-label required">Audio File</label>
                    <input type="file" name="words[${wordIndex}][audio]" class="form-control" accept=".mp3,.wav" required>
                </div>

                <div class="fv-row mb-4">
                    <label class="form-label required">Meaning</label>
                    <input type="text" name="words[${wordIndex}][meaning]" class="form-control" required>
                </div>
            </div>
        `;

            $('#words-container').append(wordHtml);
            wordIndex++;
            updateWordNumbers();
        });

        $(document).on('click', '.remove-word-btn', function () {
            $(this).closest('.word-block').remove();
            updateWordNumbers();
        });

        function updateWordNumbers() {
            $('.word-block').each(function(index) {
                $(this).find('.word-number').text(index + 1);
            });
        }
    </script>
@endpush
