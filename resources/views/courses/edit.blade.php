@extends("layouts.app")

@section('title')
    Edit Course
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="app-toolbar p-3">
                <div class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading text-dark fw-bolder fs-2">Edit Course</h1>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="card card-flush">
                        <div class="card-body pt-6">
                            <form method="POST" action="{{ route('courses.update', $course) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-7">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" value="{{ old('title', $course->title) }}" class="form-control form-control-solid mb-2" placeholder="Enter Course Title" />
                                    @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="mb-7">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control form-control-solid mb-2" rows="4" placeholder="Enter Course Description">{{ old('description', $course->description) }}</textarea>
                                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="mb-7">
                                    <label class="form-label required">Type</label>
                                    <select name="type" aria-label="Select Type" data-control="select2" data-placeholder="Select course type" class="form-select form-select-solid">
                                        <option value="">Select Type</option>
                                        @foreach(\App\Enum\CourseTypeEnum::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('type', $course->type) == $type->value ? 'selected' : '' }}>
                                                {{ ucfirst($type->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="mb-7">
                                    <label class="form-label required">Price</label>
                                    <input type="number"  name="price" value="{{ old('price',$course->price) }}" class="form-control form-control-solid mb-2" placeholder="Enter  price" />
                                    @error('price')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="mb-7">
                                    <label class="form-label">Current Image</label><br />
                                    @if($course->image)
                                        <img src="{{ Storage::disk('s3')->url($course->image) }}" width="100" alt="Course Image">
                                    @else
                                        <p>No Image Uploaded</p>
                                    @endif
                                </div>

                                <div class="mb-7">
                                    <label class="form-label">Change Image</label>
                                    <input type="file" name="image" class="form-control form-control-solid mb-2" />
                                    @error('image')<small class="text-danger">{{ $message }}</small>@enderror
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
