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
                            <label class="form-label required">Is this Level Free?</label>
                            <select name="is_free" id="is_free" class="form-select form-select-solid"
                                    data-control="select2" data-placeholder="Select option" required>
                                <option value="">Select Option</option>
                                <option value="1" {{ old('is_free', $level->is_free) == '1' ? 'selected' : '' }}>Yes (Free)</option>
                                <option value="0" {{ old('is_free', $level->is_free) == '0' ? 'selected' : '' }}>No (Paid)</option>
                            </select>
                            @error('is_free')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="mb-7" id="price_field">
                            <label class="form-label">Level Price</label>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $level->price ?? 0) }}"
                                   class="form-control form-control-solid mb-2" placeholder="Enter Price" />
                            @error('price')<small class="text-danger">{{ $message }}</small>@enderror
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
