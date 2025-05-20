@extends("layouts.app")

@section('title', 'Add Level to ' . $course->title)

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder">Add Level to Course: {{ $course->title }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ route('courses.levels.index', $course) }}" class="btn btn-light" style="border-radius: 20px">Back to Levels</a>
                    </div>
                </div>
                <div class="card-body pt-6">
                    <form method="POST" action="{{ route('courses.levels.store', $course) }}">
                        @csrf
                        <div class="mb-7">
                            <label class="form-label required">Level Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control form-control-solid mb-2" placeholder="Enter Level Title"  />
                            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="text-center pt-10">
                            <button type="submit" class="btn btn-primary" style="border-radius: 20px">
                                <span class="indicator-label">Save Level</span>
                                <span class="indicator-progress">Saving...
                                    <span class="spinner-border spinner-border-sm ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
