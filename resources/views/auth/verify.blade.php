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
                            @csrf
                            <div class="d-lg-none d-block w-100 d-flex justify-content-center">
                                <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="w-75">
                            </div>

                            <h2 class="fw-bold text-brown1">Verify your email!</h2>

                            @if (session('resent'))
                                <div class="alert alert-success" role="alert">
                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <p class="m-0">Please check your email for a verification link</p>
                            </div>

                            <form class="w-100 d-flex flex-column justify-content-center align-items-center" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <div>
                                    <p class="m-0">If you did not receive the email</p>
                                </div>
                                <button type="submit"
                                    class="btn btn-warning">{{ __('Resend verification link') }}</button>
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

{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
