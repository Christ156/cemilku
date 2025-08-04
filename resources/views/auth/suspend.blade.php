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

    <link rel="shortcut icon" href="{{ asset('assets/logo/logo_cemilku.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous">
    </script>

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

                <div
                    class="hide-scrollbar col-lg-6 col-12 d-flex flex-column justify-content-center px-lg-5 px-3 h-100">
                    <div class="hide-scrollbar h-100 w-100 overflow-scroll d-flex flex-column justify-content-center">
                        <div
                            class="my-3 px-1 d-lg-flex flex-lg-column justify-content-lg-center">\
                            <div class="d-lg-none d-block w-100 d-flex justify-content-center">
                                <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="w-75">
                            </div>

                            <h3 class="fw-bold text-brown1 w-100 text-center mb-5">Your account has been blocked!</h3>

                            <form action="{{ route('logout') }}" method="post"
                                class="w-100 mt-3 d-flex justify-content-center align-items-center">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">Login with other account</button>
                            </form>

                            <div class="my-3 w-100 d-flex justify-content-center align-items-center">
                                <img src="{{ asset('assets/layout/borderLineHiden.png') }}" alt="" class="w-100">
                            </div>

                            <div class="w-100 mt-5 d-flex justify-content-center align-items-bottom">
                                <p class="fw-bold text-brown1 text-center">Please contact admin to unblock your account
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
