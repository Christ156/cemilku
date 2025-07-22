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

                <div class="hide-scrollbar col-lg-6 col-12 d-flex flex-column justify-content-center px-lg-5 px-3 h-100">
                    <div class="hide-scrollbar h-100 w-100 overflow-scroll d-flex justify-content-center">
                        <form method="POST" action="{{ route('login') }}"
                            class="my-3">
                            @csrf
                            <div class="d-lg-none d-block w-100 d-flex justify-content-center">
                                <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="w-75">
                            </div>

                            <h1 class="fw-bold text-brown1">Welcome Back!</h1>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email"
                                    class="py-2 border border-warning bg-yellow2 form-control @error('email') is-invalid @enderror"
                                    name="email" id="email" aria-describedby="emailHelpId"
                                    placeholder="Type your email" value="{{ old('email') }}" required
                                    autocomplete="email" />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password"
                                    class="py-2 border border-warning bg-yellow2 form-control @error('password') is-invalid @enderror"
                                    name="password" id="password" placeholder="Type your password" required
                                    autocomplete="current-password" />
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="w-100 d-flex p-0 m-0 my-3 row justify-content-lg-between">
                                <div class="col-lg-5 col-12 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="border border-warning bg-yellow2 text-brown1 form-check-input"
                                            type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label text-brown1 fw-bold" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                                <div
                                    class="col-lg-7 col-12 d-flex align-items-center justify-content-start justify-content-lg-end text-lg-end">
                                    @if (Route::has('password.request'))
                                        <a class="fw-bold text-yellow1" href="{{ route('password.request') }}">
                                            Forgot Your Password?
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <input type="submit" value="Log in" class="btn btn-warning py-2 w-100 fw-bold text-brown1">

                            <div class="my-3 w-100 d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/layout/borderLine.png') }}" alt="" class="w-100">
                            </div>

                            <a href="{{route('google-redirect')}}" class="btn btn-warning py-2 w-100 fw-bold text-brown1">Sign up with Google</a>

                            <div class="w-100 mt-5 d-flex justify-content-center align-items-bottom">
                                <p class="fw-bold text-brown1 text-center">Don't have an account? <span><a
                                            class="fw-bold text-yellow1" href="{{ route('register') }}">Register
                                            here</a></span>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

{{-- @section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
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

                        <div class="row mb-3">
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

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
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
@endsection --}}
