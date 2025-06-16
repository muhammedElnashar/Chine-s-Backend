@extends('layouts.app')

@section('title', 'Show Exam' . $exercise->title)

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="app-toolbar p-3">
                <div class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-dark fw-bolder fs-2"> {{ $exercise->title }}</h1>
                    </div>
                    <div>
                        <a href="{{ route('exercises.index') }}" class="btn btn-secondary" style="border-radius: 20px;">
                            Return to Exam List
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="card card-flush shadow-sm" style="border-radius: 25px; background-color: #f9fafb;">
                        <div class="card-body pt-6">
                            @if($exercise->description)
                                <p class="text-secondary fst-italic mb-5" style="font-size: 1.1rem;">{{ $exercise->description }}</p>
                            @endif

                            @foreach($exercise->questions as $index => $question)
                                <div class="mb-5 pb-4 border-bottom border-2 border-secondary-subtle">
                                    <h5 class="fw-bold mb-4 d-flex align-items-center" style="color: #1e293b;">
        <span class="badge rounded-pill bg-indigo text-white me-3"
              style="font-size: 1.2rem; min-width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
            {{ $index + 1 }}
        </span>


                                        @if($question->question_type === 'text')
                                            {{ $question->question_text }}
                                        @elseif($question->question_type === 'image')
                                            <img src=" {{ Storage::disk('s3')->url($question->question_media_url) }}" alt="Question Image" class="img-fluid rounded"  style="max-height: 100px;">
                                        @elseif($question->question_type === 'video')
                                            <video controls class="w-100" style="max-height: 400px;">
                                                <source src="{{ Storage::disk('s3')->url($question->question_media_url) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @elseif($question->question_type === 'audio')
                                            <audio controls>
                                                <source src="{{ Storage::disk('s3')->url($question->question_media_url) }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        @endif
                                    </h5>

                                    {{-- الإجابات --}}
                                    <div class="row g-3">
                                        @foreach($question->answers->chunk(2) as $answerPair)
                                            @foreach($answerPair as $answerIndex => $answer)
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded shadow-sm
                    @if($answer->is_correct)
                        bg-success bg-opacity-15 border border-success
                    @else
                        bg-white border border-light
                    @endif
                    ">
                                                        <div class="d-flex align-items-center">
                            <span class="badge rounded-circle
                                @if($answer->is_correct) bg-success text-white @else bg-primary text-white @endif
                                me-3"
                                  style="min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                {{ chr(65 + $loop->parent->index * 2 + $loop->index) }}
                            </span>
                                                            <span class="fs-6" style="color: #334155;">{{ $answer->answer_text }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>

                                    {{-- التوضيح إن وُجد --}}
                                    @if($question->explanation)
                                        <div class="mt-4 p-4 bg-light border-start border-4 border-info rounded">
                                            <strong>Explanation:</strong>
                                            <p class="mb-0">{{ $question->explanation }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-indigo {
            background-color: #5c6ac4 !important;
        }
        .bg-indigo:hover {
            background-color: #4f5acb !important;
        }
    </style>
@endsection
