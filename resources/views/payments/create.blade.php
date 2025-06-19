@extends("layouts.app")

@section('title')
    Create Payment
@endsection

@section('content')
    <div class="app-content flex-column-fluid">
        <div class="app-container container-xxl">
            <div class="card mx-auto" style="border-radius: 25px; ">
                <div class="card-header">
                    <h3 class="card-title">Add Payment</h3>
                </div>
                <div class="card-body pt-6">
                    <form method="POST" action="{{ route('payments.store') }}"  >
                        @csrf
                        <div class="mb-7">
                            <label class="form-label required">Users</label>
                            <select name="user_id" aria-label="Select User" data-control="select2" data-placeholder="Select User" class="form-select form-select-solid">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ ucfirst($user->name) }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="mb-7">
                            <label class="form-label required">Courses</label>
                            <select id="courseSelect" name="course_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Select Course">
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-price="{{ $course->price }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ ucfirst($course->title) }} - ${{ $course->price }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <input type="hidden" name="amount" id="coursePrice" value="{{ old('amount') }}">


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
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#courseSelect').on('change', function () {
                let selectedPrice = $(this).find(':selected').data('price');
                $('#coursePrice').val(selectedPrice || '');
            });

            // في حال كانت الدورة مختارة مسبقًا عند العودة من فشل تحقق
            let initialPrice = $('#courseSelect').find(':selected').data('price');
            if (initialPrice) {
                $('#coursePrice').val(initialPrice);
            }
        });
    </script>
@endpush
