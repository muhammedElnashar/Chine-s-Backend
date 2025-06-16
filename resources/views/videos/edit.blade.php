@extends("layouts.app")

@section('title')
    Edit Video
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="app-toolbar p-3">
                <div class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-dark fw-bolder fs-2">Edit Video</h1>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form method="POST" action="{{ route('courses.levels.videos.update',[$course,$level,$video]) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-7">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" value="{{ old('title', $video->title) }}" class="form-control form-control-solid mb-2" placeholder="Enter Video Title" />
                                    @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>


                                <div class="text-center pt-15">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Save Changes</span>
                                        <span class="indicator-progress">جاري الحفظ...
                                            <span class="spinner-border spinner-border-sm ms-2"></span>
                                        </span>
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
