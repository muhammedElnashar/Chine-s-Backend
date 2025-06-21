@extends("layouts.app")

@section('title')
    Create User
@endsection

@section('content')
    <div class="app-content flex-column-fluid">
        <div class="app-container container-xxl">
            <div class="card mx-auto" style="border-radius: 25px; ">
                <div class="card-header">
                    <h3 class="card-title">Add User</h3>
                </div>
                <div class="card-body pt-6">
                    <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" >
                        @csrf

                        <div class="mb-7">
                            <label class="form-label required">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-solid mb-2" placeholder="Enter Username" />
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="mb-7">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-solid mb-2" placeholder="Enter Email" />
                            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="mb-7">
                            <label class="form-label required">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password-field"
                                       class="form-control  " placeholder="Enter Password"/>
                                <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer"><i class="fa fa-eye"></i></span>
                            </div>
                            @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="mb-7">
                            <label class="form-label required">Role</label>
                            <select name="role" aria-label="Select Role" data-control="select2" data-placeholder="Select User Role" class="form-select form-select-solid">
                                <option value="">Select Role</option>
                                @foreach(\App\Enum\UserRoleEnum::cases() as $type)
                                    @if(in_array($type->value, ['admin', 'super_admin']))
                                        <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $type->value)) }}
                                        </option>
                                    @endif
                                @endforeach

                            </select>
                            @error('role')<small class="text-danger">{{ $message }}</small>@enderror
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

@endsection
@push('script')
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password-field');
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
@endpush
