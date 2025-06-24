@extends('layouts.app')

@section('title', 'Add New File')

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Add New File</h3>
                    <a href="{{ route('courses.levels.files.index', [$course, $level]) }}" class="btn btn-light"
                       style="border-radius: 20px;">Back to Files</a>
                </div>

                <div class="card-body pt-0">
                    <form  action="{{ route('courses.levels.files.store', [$course, $level]) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        <div id="videos-container">
                            <div class="video-item mb-6" data-index="0">
                                <label class="form-label required">Name</label>
                                <input type="text" name="name" value="{{old('name')}}" class="form-control "
                                       placeholder="Enter File Name" >
                                @error('name')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror

                                <label class="form-label mt-3">Upload File</label>
                                <input type="file" name="file"  class="form-control " >
                                @error('file')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit"  class="btn btn-primary" >Upload File</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

