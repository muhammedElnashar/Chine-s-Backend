@extends("layouts.app")

@section('title')
    Edit Announcement
@endsection
@push("css")
@endpush

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

        <div class=" d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar p-3 ">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bolder fs-2 flex-column justify-content-center my-0">
                            Edit Announcement </h1>

                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush">


                        <div class="card-body pt-6">
                            <form id="kt_modal_add_form" class="form" method="POST"
                                  action="{{route("announcements.update",$announcement)}}" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 ">
                                        <span class="required"> Title</span>
                                        <span>
                                            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                                        </span>
                                    </label>

                                    <div class="input-group input-group-solid mb-5">
                                        <input type="text" value="{{$announcement->title}}" class="form-control"
                                               name="title"
                                               placeholder="Enter Title" autocomplete="off"/>

                                    </div>

                                </div>
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 ">
                                        <span class="required"> Content</span>
                                        <span>
                                            @error('content')<small class="text-danger">{{ $message }}</small>@enderror
                                        </span>
                                    </label>

                                    <div class="input-group input-group-solid mb-5">
                                        <textarea class="form-control" name="content" placeholder="Enter Content"
                                                  autocomplete="off" rows="4">{{ $announcement->content}}</textarea>
                                    </div>
                                </div>
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2">
                                        <span class="">Image</span>
                                        <span>
                                            @error('image')<small class="text-danger">{{ $message }}</small>@enderror
                                        </span>
                                    </label>

                                    <div class="input-group input-group-solid mb-5">
                                        <input type="file" class="form-control" name="image"  placeholder="Add Image"
                                               autocomplete="off"/>
                                    </div>
                                    <div class="input-group  mb-5">
                                        @if($announcement->image)
                                        <img src="{{ Storage::disk('s3')->url($announcement->image) }} " alt="" width="50px" height="50px">
                                        @endif
                                    </div>


                                </div>
                                <div class="fv-row mb-7">
                                    <label class="fs-6 fw-semibold form-label mb-2 ">
                                        <span class="">Url</span>
                                        <span>
                                            @error('url')<small class="text-danger">{{ $message }}</small>@enderror
                                        </span>
                                    </label>

                                    <div class="input-group input-group-solid mb-5">
                                        <input type="text" value="{{$announcement->url}}" class="form-control"
                                               name="url"
                                               placeholder="Enter Url" autocomplete="off"/>

                                    </div>

                                </div>
                                <div class="text-center pt-15">

                                    <button type="submit" class="btn btn-primary" data-kt-modal-action="submit">
                                        <span class="indicator-label">Save</span>
                                        <span class="indicator-progress">جاري الحفظ ...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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


