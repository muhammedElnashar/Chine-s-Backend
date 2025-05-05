{{--@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3 row">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0 row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection--}}

    <!DOCTYPE html>

<html lang="en">
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{asset("assets/plugins/global/plugins.bundle.css")}}" rel="stylesheet" type="text/css" />
    <link href="{{asset("assets/css/style.bundle.css")}}" rel="stylesheet" type="text/css" />
</head>

<body id="kt_body" class="bg-body">
@include('partials.alert')

<div class="d-flex flex-column flex-root">

    <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(assets/media/illustrations/sketchy-1/14.png">
        <div class="p-10 d-flex flex-center flex-column flex-column-fluid pb-lg-20">
            <div class="mb-12">
                <img alt="Logo" src="{{asset("assets/media/logos/logo-1.svg")}}" class="h-40px" />
            </div>

            <div class="p-10 mx-auto rounded shadow-sm w-lg-500px bg-body p-lg-15">
                <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" method="post" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-10 text-center">
                        <h1 class="mb-3 text-dark">Sign In </h1>
                    </div>
                    <div class="mb-10 fv-row">
                        <label class="form-label fs-6 fw-bolder text-dark">Email</label>
                        <input name="email"  class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" value="{{old("email")}}" required autocomplete="email" autofocus  type="text"  />
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="mb-10 fv-row">
                        <div class="mb-2 d-flex flex-stack">
                            <label class="mb-0 form-label fw-bolder text-dark fs-6">Password</label>

                        </div>

                        <input class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror" type="password" name="password" autocomplete="off" />

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <!--end::Input group-->
                    <!--begin::Actions-->
                    <div class="text-center">
                        <!--begin::Submit button-->
                        <button type="submit" id="kt_sign_in_submit" class="mb-5 btn btn-lg btn-primary w-100">
                            <span class="indicator-label">Log in</span>
                            <span class="indicator-progress">Please wait...
									<span class="align-middle spinner-border spinner-border-sm ms-2"></span></span>
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>var hostUrl = "assets/";</script>
<script src="{{asset("assets/plugins/global/plugins.bundle.js")}}"></script>
<script src="{{asset("assets/js/scripts.bundle.js")}}"></script>

<script src="{{asset("assets/js/custom/authentication/sign-in/general.js")}}"></script>

</body>
</html>
