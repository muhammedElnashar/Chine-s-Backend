@extends("layouts.app")

@section('title')
Add Daily Exercise
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar p-3">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                            Add Daily Exercise
                        </h1>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form id="daily-exercise-form" class="form" method="POST" action="{{ route('questions.store') }}">
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

                                <div id="questions-container">
                                    @for ($i = 0; $i < 2; $i++)
                                        <div class="question-block border rounded p-4 mb-7">
                                            <h4 class="mb-3">Question Number <span class="question-number">{{ $i + 1 }}</span></h4>

                                            <div class="fv-row mb-4">
                                                <label class="form-label required">Question Text</label>
                                                <input type="text" name="questions[{{ $i }}][question_text]" class="form-control" required >
                                                @error("questions.$i.question_text")
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <label class="form-label">Answers</label>
                                            @for ($j = 0; $j < 4; $j++)
                                                <div class="input-group input-group-solid mb-3">
                                                    <input type="text" name="questions[{{ $i }}][answers][]" class="form-control" placeholder="Answer {{ $j + 1 }}" required >
                                                    <div class="input-group-text">
                                                        <input type="radio" name="questions[{{ $i }}][correct_answer]" value="{{ $j }}" required >
                                                        <span class="ms-2">Correct Answer</span>
                                                    </div>
                                                </div>
                                                @error("questions.$i.answers.$j")
                                                <small class="text-danger d-block">{{ $message }}</small>
                                                @enderror
                                            @endfor

                                            @error("questions.$i.correct_answer")
                                            <small class="text-danger d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endfor
                                </div>

                                <div class="text-start mb-5">
                                    <button type="button" class="btn btn-secondary" id="add-question-btn">Add Another Question</button>
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
        let questionIndex = 2;

        $('#add-question-btn').on('click', function () {
            const questionHtml = `
        <div class="question-block border rounded p-4 mb-7 position-relative">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">
                Delete Question
            </button>

            <h4 class="mb-3">Question Number <span class="question-number">${questionIndex + 1}</span></h4>

            <div class="fv-row mb-4">
                <label class="form-label required">Question Text</label>
                <input type="text" name="questions[${questionIndex}][question_text]" class="form-control" required>
            </div>

            <label class="form-label">Answers</label>
            ${[0,1,2,3].map(i => `
                <div class="input-group input-group-solid mb-3">
                    <input type="text" name="questions[${questionIndex}][answers][]" class="form-control" placeholder="Answer ${i + 1}" required>
                    <div class="input-group-text">
                        <input type="radio" name="questions[${questionIndex}][correct_answer]" value="${i}" required>
                        <span class="ms-2">Correct Answer</span>
                    </div>
                </div>
            `).join('')}
        </div>`;

            $('#questions-container').append(questionHtml);
            questionIndex++;
            updateQuestionNumbers();
        });

        $(document).on('click', '.remove-question-btn', function () {
            $(this).closest('.question-block').remove();
            updateQuestionNumbers(); //
        });

        function updateQuestionNumbers() {
            $('.question-block').each(function(index) {
                $(this).find('.question-number').text(index + 1);
            });
        }
    </script>
@endpush
