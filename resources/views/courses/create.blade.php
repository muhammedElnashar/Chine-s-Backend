@extends("layouts.app")

@section('title')
    Create Course
@endsection

@section('content')
            <div class="app-content flex-column-fluid">
                <div class="app-container container-xxl">
                    <div class="card mx-auto" style="border-radius: 25px; ">
                        <div class="card-header">
                            <h3 class="card-title">Add Course</h3>
                        </div>
                        <div class="card-body pt-6">
                            <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data" >
                                @csrf

                                <div class="mb-7">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="form-control form-control-solid mb-2" placeholder="Enter Course Title" />
                                    @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>

                                <div class="mb-7">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control form-control-solid mb-2" rows="4" placeholder="Enter Course Description">{{ old('description') }}</textarea>
                                    @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="mb-7">
                                    <label class="form-label required">Type</label>
                                    <select name="type" aria-label="Select Type" data-control="select2" data-placeholder="Select course type" class="form-select form-select-solid">
                                        <option value="">Select Type</option>
                                        @foreach(\App\Enum\CourseTypeEnum::cases() as $type)
                                            <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                                {{ ucfirst($type->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="mb-7">
                                    <label class="form-label ">Image</label>
                                    <input type="file" name="image" class="form-control form-control-solid mb-2" />
                                    @error('image')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>


                                <div class="text-center pt-15">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Save</span>
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
