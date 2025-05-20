@extends('layouts.app')

@section('title', 'Edit Level')

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Edit Level: {{ $level->title }}</h3>
                    <a href="{{ route('courses.levels.index', $course) }}" class="btn btn-light"
                       style="border-radius: 20px">Back to Levels</a>
                </div>

                <div class="card-body pt-0">
                    <form action="{{ route('courses.levels.update', [$course, $level]) }}" method="POST" class="form">
                        @csrf
                        @method('PUT')
                        <div class="mb-7">
                            <label class="form-label required">Level Title</label>
                            <input type="text" name="title" value="{{ old('title', $level->title) }}"
                                   class="form-control form-control-solid mb-2" placeholder="Enter Level Title"/>
                            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary" style="border-radius: 20px;">Update Level</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
