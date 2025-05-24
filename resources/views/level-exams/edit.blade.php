@extends("layouts.app")

@section('title', 'Edit Exam')

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="app-toolbar p-3">
                <div class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-dark fw-bolder fs-2">Edit Exam for Level: {{ $level->title }}</h1>
                    </div>
                    <div>
                        <a href="{{ route('courses.levels.show', [$course, $level]) }}" class="btn btn-light" style="border-radius: 20px;">Back to Level</a>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form method="POST" action="{{ route('courses.levels.exams.update', [$course, $level, $exam]) }}">
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
                                        <div class="question-block border rounded p-4 mb-7 position-relative">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">
                                                Delete
                                            </button>

                                            <h4 class="mb-3">Question <span class="question-number">{{ $qIndex + 1 }}</span></h4>

                                            <input type="hidden" name="questions[{{ $qIndex }}][id]" value="{{ $question->id }}">

                                            <div class="mb-4">
                                                <label class="form-label required">Question Text</label>
                                                <input type="text" name="questions[{{ $qIndex }}][question_text]" class="form-control" value="{{ $question->question_text }}" required>
                                            </div>

                                            @foreach($question->answers as $aIndex => $answer)
                                                <div class="input-group input-group-solid mb-3">
                                                    <input type="text" name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][text]" class="form-control" placeholder="Answer {{ $aIndex + 1 }}" value="{{ $answer->answer_text }}" required>
                                                    <input type="hidden" name="questions[{{ $qIndex }}][answers][{{ $aIndex }}][id]" value="{{ $answer->id }}">
                                                    <div class="input-group-text">
                                                        <input type="radio" name="questions[{{ $qIndex }}][correct_answer]" value="{{ $aIndex }}" {{ $answer->is_correct ? 'checked' : '' }}>
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

        $('#add-question-btn').on('click', function () {
            const questionHtml = `
        <div class="question-block border rounded p-4 mb-7 position-relative">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-question-btn">Delete</button>
            <h4 class="mb-3">Question <span class="question-number">${questionIndex + 1}</span></h4>
            <div class="mb-4">
                <label class="form-label required">Question Text</label>
                <input type="text" name="questions[${questionIndex}][question_text]" class="form-control" required>
            </div>
            ${[0,1,2,3].map(i => `
                <div class="input-group input-group-solid mb-3">
                    <input type="text" name="questions[${questionIndex}][answers][${i}][text]" class="form-control" placeholder="Answer ${i + 1}" required>
                    <div class="input-group-text">
                        <input type="radio" name="questions[${questionIndex}][correct_answer]" value="${i}">
                        <span class="ms-2">Correct</span>
                    </div>
                </div>`).join('')}
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
            });
        }
    </script>
@endpush
