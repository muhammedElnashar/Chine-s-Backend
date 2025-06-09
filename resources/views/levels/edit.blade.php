@extends('layouts.app')

@section('title', 'Edit Level: ' . $level->title)

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xl">
            <div class="card mx-auto" style="border-radius: 25px;">
                <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bolder">Edit Level: {{ $level->title }}</h3>
                    <a href="{{ route('courses.levels.index', $course) }}" class="btn btn-light" style="border-radius: 20px">Back to Levels</a>
                </div>

                <div class="card-body pt-6">
                    <form method="POST" action="{{ route('courses.levels.update', [$course, $level]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-7">
                            <label class="form-label required">Level Title</label>
                            <input type="text" name="title" value="{{ old('title', $level->title) }}"
                                   class="form-control form-control-solid mb-2" placeholder="Enter Level Title" />
                            @error('title')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>


                        <div class="mb-7">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control form-control-solid mb-2" rows="4" placeholder="Enter Course Description">{{ old('description', $level->description) }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="text-center pt-10">
                            <button type="submit" class="btn btn-primary" style="border-radius: 20px">
                                <span class="indicator-label">Update Level</span>
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

@push('script')
    <script>
        $(document).ready(function () {
            function togglePriceField() {
                var val = $('#is_free').val();
                if (val === "0") {
                    $('#price_field').show();
                } else {
                    $('#price_field').hide();
                    $('#price_field input').val(0);
                }
            }

            $('#is_free').on('change', togglePriceField);

            togglePriceField();
        });
    </script>
@endpush
