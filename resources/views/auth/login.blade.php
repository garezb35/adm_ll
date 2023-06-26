@extends('layouts.master-without-nav')
@section('title')
@lang('translation.signin')
@endsection
@section('content')

<section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card mb-0">
                    <div class="row g-0 align-items-center">
                        <div class="col-xxl-5">
                            <div class="card auth-card bg-secondary h-100 border-0 shadow-none d-none d-sm-block mb-0">
                                <div class="card-body py-5 d-flex justify-content-between flex-column">
                                    <div class="text-center">
                                        <h3 class="text-white">Start your journey with us.</h3>
{{--                                        <p class="text-white opacity-75 fs-base">It brings together your tasks, projects, timelines, files and more</p>--}}
                                    </div>

                                    <div class="auth-effect-main my-5 position-relative rounded-circle d-flex align-items-center justify-content-center mx-auto">
                                        <div class="effect-circle-1 position-relative mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                            <div class="effect-circle-2 position-relative mx-auto rounded-circle d-flex align-items-center justify-content-center">
                                                <div class="effect-circle-3 mx-auto rounded-circle position-relative text-white fs-4xl d-flex align-items-center justify-content-center">
                                                    Welcome to <span class="text-primary ms-1">{{env('APP_NAME')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="auth-user-list list-unstyled">
                                            <li>
                                                <div class="avatar-sm d-inline-block">
                                                    <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                        <img src="build/images/users/avatar-1.jpg" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="avatar-sm d-inline-block">
                                                    <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                        <img src="build/images/users/avatar-2.jpg" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="avatar-sm d-inline-block">
                                                    <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                        <img src="build/images/users/avatar-3.jpg" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="avatar-sm d-inline-block">
                                                    <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                        <img src="build/images/users/avatar-4.jpg" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="avatar-sm d-inline-block">
                                                    <div class="avatar-title bg-white shadow-lg overflow-hidden rounded-circle">
                                                        <img src="build/images/users/avatar-5.jpg" alt="" class="img-fluid">
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-6 mx-auto">
                            <div class="card mb-0 border-0 shadow-none mb-0">
                                <div class="card-body p-sm-5 m-lg-4">
                                    <div class="text-center mt-5">
                                        <h5 class="fs-3xl">Admin {{env('APP_NAME')}}</h5>
{{--                                        <p class="text-muted">Sign in to continue to Steex.</p>--}}
                                    </div>
                                    <div class="p-2 mt-5">
                                        <form action="{{ route('login')}}" method="post">
                                            @csrf

                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" class="form-control @error('userid') is-invalid @enderror" value="{{ old('userid', '') }}" id="userid" name="userid" placeholder="Enter userid">
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password-input">Password <span class="text-danger">*</span></label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input type="password" class="form-control password-input pe-5 @error('password') is-invalid @enderror" id="password-input"  name="password"  placeholder="Enter password"  value="">
                                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                                <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-primary w-100" type="submit">로그인</button>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>

@endsection
@section('script')

<script src="{{ URL::asset('build/js/pages/password-addon.init.js') }}"></script>
<script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ URL::asset('build/js/pages/swiper.init.js') }}"></script>

@endsection
