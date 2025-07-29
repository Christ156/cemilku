<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login | CemilKu</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/loginRegister.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="position-relative d-flex justify-content-center align-items-center vh-100">
        <div class="z-0 position-absolute opacity-75" style="width: 100vw; height: 100%; background: black;"></div>
        <div class="z-1 container d-flex justify-content-center align-items-center px-lg-0 px-5 my-5">
            <div class="row d-flex justify-content-center p-0 rounded-lg-5 rounded-4 bg-yellow2 shadow"
                style="height: 80vh">
                <div
                    class="overflow-hidden h-100 col-lg-6 col-12 d-flex justify-content-center align-items-center d-lg-block d-none p-0 bg-brown1 rounded-start-4">
                    <img src="{{ asset('assets/layout/imageLogin.png') }}" alt=""
                        class="w-100 h-100 rounded-start-4">
                </div>

                <div class="col-lg-6 col-12 d-flex flex-column justify-content-center px-lg-5 px-3 h-100">
                    <div class="h-100 w-100 overflow-scroll d-flex justify-content-center hide-scrollbar">
                        <div class="d-flex flex-column align-items-center justify-content-center my-3">
                            <div class="d-lg-none d-block w-100 d-flex justify-content-center">
                                <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="w-75">
                            </div>

                            <h2 class="w-100 fw-bold text-brown1">Reset Password</h2>

                            <form method="POST" action="{{ route('password.update') }}" class="w-100 px-1">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="row mb-3">
                                    <label for="email"
                                        class="col-form-label text-md-start">{{ __('Email Address') }}</label>

                                    <div class="">
                                        <input id="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror border border-warning bg-yellow2" name="email"
                                            value="{{ $email ?? old('email') }}" required autocomplete="email"
                                            autofocus>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password"
                                        class="col-form-label text-md-start">{{ __('Password') }}</label>

                                    <div class="">
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror border border-warning bg-yellow2" name="password"
                                            required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password-confirm"
                                        class="col-form-label text-md-start">{{ __('Confirm Password') }}</label>

                                    <div class="">
                                        <input id="password-confirm" type="password" class="form-control border border-warning bg-yellow2"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-warning">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="w-100">
                                <img src="{{ asset('assets/layout/borderLineHiden.png') }}" alt=""
                                    class="w-100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>


{{-- @section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
