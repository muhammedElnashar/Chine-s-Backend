@extends("layouts.app")

@section('title')
    Create Level Exam
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar p-3">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                            Create Exam for Level: {{ $level->title ?? '' }}
                        </h1>
                    </div>
                    <div>
                        <a href="{{ route('courses.levels.show', [$course, $level]) }}" class="btn btn-light" style="border-radius: 20px;">Back to Level</a>
                    </div>
                </div>
            </div>

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form id="exam-form" method="POST" action="{{ route('courses.levels.exams.store', [$course, $level]) }}">
                                @csrf

                                <div class="mb-7">
                                    <label for="title" class="form-label required">Exam Title</label>
                                    <input type="text" id="title" name="title" class="form-control form-control-solid @error('title') is-invalid @enderror"
                                           value="{{ old('title') }}" required>
                                    @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-7">
                                    <label for="description" class="form-label">Exam Description (optional)</label>
                                    <textarea id="description" name="description" class="form-control form-control-solid @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <hr>

                                <div id="questions-container">
                                    <h3 class="mb-5">Questions</h3>
                                    @if(old('questions'))
                                        @foreach(old('questions') as $qIndex => $question)
                                            <div class="question-block border rounded p-4 mb-7 position-relative">
                                                @if($qIndex >= 10)
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">
                                                        Delete Question
                                                    </button>
                                                @endif

                                                <h4 class="mb-3">Question <span class="question-number">{{ $qIndex + 1 }}</span></h4>

                                                <div class="mb-4">
                                                    <label class="form-label required">Question Text</label>
                                                    <input type="text" name="questions[{{ $qIndex }}][question_text]" class="form-control @error("questions.$qIndex.question_text") is-invalid @enderror" value="{{ $question['question_text'] }}" required>
                                                    @error("questions.$qIndex.question_text")
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>

                                                <label class="form-label">Answers</label>
                                                @foreach($question['answers'] as $aIndex => $answer)
                                                    <div class="input-group input-group-solid mb-3">
                                                        <input type="text" name="questions[{{ $qIndex }}][answers][]" class="form-control @error("questions.$qIndex.answers.$aIndex") is-invalid @enderror" placeholder="Answer {{ $aIndex + 1 }}" value="{{ $answer }}" required>
                                                        <div class="input-group-text">
                                                            <input type="radio" name="questions[{{ $qIndex }}][correct_answer]" value="{{ $aIndex }}" {{ (isset($question['correct_answer']) && $question['correct_answer'] == $aIndex) ? 'checked' : '' }} required>
                                                            <span class="ms-2">Correct Answer</span>
                                                        </div>
                                                    </div>
                                                    @error("questions.$qIndex.answers.$aIndex")
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                @endforeach

                                                @error("questions.$qIndex.correct_answer")
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        @endforeach
                                    @else
                                        @for ($i = 0; $i < 1; $i++)
                                            <div class="question-block border rounded p-4 mb-7 position-relative">
                                                <h4 class="mb-3">Question <span class="question-number">{{ $i + 1 }}</span></h4>

                                                <div class="mb-4">
                                                    <label class="form-label required">Question Text</label>
                                                    <input type="text" name="questions[{{ $i }}][question_text]" class="form-control" required>
                                                </div>

                                                <label class="form-label">Answers</label>
                                                @for ($j = 0; $j < 4; $j++)
                                                    <div class="input-group input-group-solid mb-3">
                                                        <input type="text" name="questions[{{ $i }}][answers][]" class="form-control" placeholder="Answer {{ $j + 1 }}" required>
                                                        <div class="input-group-text">
                                                            <input type="radio" name="questions[{{ $i }}][correct_answer]" value="{{ $j }}" required>
                                                            <span class="ms-2">Correct Answer</span>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        @endfor
                                    @endif
                                </div>

                                <div class="mb-5">
                                    <button type="button" class="btn btn-secondary" id="add-question-btn">Add Another Question</button>
                                </div>

                                <div class="text-center pt-10">
                                    <button type="submit" class="btn btn-primary" style="border-radius: 20px;">
                                        <span class="indicator-label">Create Exam</span>
                                        <span class="indicator-progress">Saving...
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
        let questionIndex = {{ old('questions') ? count(old('questions')) : 10 }};

        $('#add-question-btn').on('click', function () {
            const questionHtml = `
        <div class="question-block border rounded p-4 mb-7 position-relative">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">
                Delete Question
            </button>

            <h4 class="mb-3">Question <span class="question-number">${questionIndex + 1}</span></h4>

            <div class="mb-4">
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
            updateQuestionNumbers();
        });

        function updateQuestionNumbers() {
            $('.question-block').each(function(index) {
                $(this).find('.question-number').text(index + 1);

                // Show delete button only for questions index >= 10 (zero based)
                if(index < 10){
                    $(this).find('.remove-question-btn').hide();
                } else {
                    $(this).find('.remove-question-btn').show();
                }
            });
        }

        // Initialize on page load
        updateQuestionNumbers();
    </script>
@endpush
