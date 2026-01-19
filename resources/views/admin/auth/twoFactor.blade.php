@extends('admin.layouts.master-without-nav')

@section('title')
    @lang('translation.Login')
@endsection

@section('css')
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.css') }}">
@endsection

@section('body')

    <body class="auth-body-bg">
    @endsection

    @section('content')
        <div>
            <div class="container-fluid p-0">
                <div class="row g-0">

                    <div class="col-xl-9">
                        <div class="auth-full-bg">
                            {{--                            <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel" --}}
                            {{--                                 data-bs-interval="3000"> --}}
                            {{--                                <div class="carousel-inner" role="listbox"> --}}
                            {{--                                    <div class="carousel-item active"> --}}
                            {{--										<img class="d-block img-fluid" src="{{ URL::asset('/assets/images/slider/xuv700.jpg') }}" --}}
                            {{--											 alt="Third slide"> --}}
                            {{--									</div> --}}
                            {{--									<div class="carousel-item "> --}}
                            {{--										<img class="d-block img-fluid" src="{{ URL::asset('/assets/images/slider/Thar.jpg') }}" --}}
                            {{--											 alt="Third slide"> --}}
                            {{--									</div> --}}
                            {{--									<div class="carousel-item "> --}}
                            {{--										<img class="d-block img-fluid" src="{{ URL::asset('/assets/images/slider/alturas-g4.jpg') }}" --}}
                            {{--											 alt="Third slide"> --}}
                            {{--									</div> --}}
                            {{--                                </div> --}}
                            {{--                            </div> --}}
                        </div>
                    </div>
                    <!-- end col -->

                    <div class="col-xl-3">
                        <div class="auth-full-page-content p-md-5 p-4">
                            <div class="w-100">
                                <div class="d-flex flex-column h-100">
                                    <div class="mb-4 mb-md-5">
                                        <a href="index" class="d-block auth-logo">
                                            <img src="{{ asset('assets/images') . DIRECTORY_SEPARATOR . config('constants.LOGO_FILE_NAME') }}"
                                                alt="" height="50" class="auth-logo-dark">
                                            <img src="{{ asset('assets/images') . DIRECTORY_SEPARATOR . config('constants.LOGO_FILE_NAME') }}"
                                                alt="" height="50" class="auth-logo-light">
                                        </a>
                                    </div>
                                    <div class="my-auto">
                                        <div class="text-center">
                                            <div class="avatar-md mx-auto">
                                                <div class="avatar-title rounded-circle bg-light">
                                                    <i class="bx bxs-envelope h1 mb-0 text-primary"></i>
                                                </div>
                                            </div>

                                            <div class="p-2 mt-4">
                                                <h4>Verify your mobile</h4>
                                                <p class="mb-5">Please enter the 6 digit code sent to <span
                                                        class="fw-semibold">{{ app('common')->maskPhoneNumber(auth()->user()->mobile) }}</span></p>

                                                <form method="POST" action="{{ route('verify.store') }}">
                                                    {{ csrf_field() }}
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="mb-3">

                                                                <input type="text"
                                                                    oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                                                    maxlength="6" name="two_factor_code"
                                                                    class="form-control @error('two_factor_code') is-invalid @enderror"
                                                                    placeholder="Enter 6 digits otp ">

                                                                @error('two_factor_code')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                            @if (auth()->user()->is_account_locked == 'N')
                                                            <div class="float-end">
                                                                <a href="{{ route('verify.resend') }}" class="text-muted">Re
                                                                    send otp</a>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div class="mt-3 d-grid">
                                                            <button class="btn btn-primary waves-effect waves-light"
                                                                type="submit">Confirm
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="mt-4 mt-md-5 text-center">
                                        Copyright © {{ \Carbon\Carbon::now()->format('Y') }} Mahindra & Mahindra Ltd. All
                                        rights
                                        reserved.
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container-fluid -->
        </div>
    @endsection
    @section('script')
        <!-- owl.carousel js -->
        <script src="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>
        <!-- auth-2-carousel init -->
        <script src="{{ URL::asset('/assets/js/pages/auth-2-carousel.init.js') }}"></script>
    @endsection
