<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{asset('assets/logo/logo_cemilku.png')}}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cemilku') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    {{-- Style --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        body,
        html {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>

    @yield('style')

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Global JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>

    @yield('script')
</head>

<body>
    <div id="app">
        <nav class="navbar fixed-top navbar-expand-sm navbar-light color_primary">
            <div class="container-fluid px-3 flex-wrap align-items-center justify-content-between">

                {{-- Toggler Kiri --}}
                <div class="d-flex align-items-center justify-content-center d-sm-none">
                    <!-- Burger hanya muncul di mobile -->
                    <button class="navbar-toggler d-block me-2" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Logo (mobile) diluar burger -->
                    <div class="d-block mb-3 text-center ms-2">
                        <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" width="80" height="42"
                            style="display: block; position: relative; top:7px" />
                    </div>
                </div>

                {{-- Desktop Logo --}}
                <a class="navbar-brand d-none d-sm-block m-4 p-2 align-items-center" href="{{ route('home') }}">
                    <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" width="85"
                        class="d-inline-block align-text-top" />
                </a>

                <!-- Menu NAVBAR Desktop -->
                <div class="collapse navbar-collapse d-none d-sm-flex" id="collapsibleNavId">
                    <ul class="navbar-nav fs-5 flex-row gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">{{ __('navigation.home') }}</a>
                        </li>
                        {{-- <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('custom') ? 'active' : '' }}"
                                href="{{ route('custom') }}">Custom</a>
                        </li> --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('collections*') ? 'active' : '' }}"
                                href="{{ route('collections.index') }}">{{ __('navigation.collections') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('order') ? 'active' : '' }}"
                                href="{{route('orders.index')}}">{{ __('navigation.order') }}</a>
                        </li>
                    </ul>
                </div>


                {{-- Burger Content --}}
                <div class="offcanvas offcanvas-start sidebar d-block d-sm-none" tabindex="-1" id="offcanvasNav"
                    aria-labelledby="offcanvasNavLabel" style="width: 50%;">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavLabel">Menu</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">

                        <!-- Logo (mobile) dalem burger -->
                        <div class="logo-burger d-block d-sm-none mb-3 ">
                            <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" width="100"
                                height="60" />
                        </div>

                        <!-- Menu -->
                        <ul class="navbar-nav fs-5 flex-column gap-2">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                    href="{{ route('home') }} ">{{ __('navigation.home') }}</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('custom') ? 'active' : '' }}"
                                    href="custom">Custom</a>
                            </li> --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('collections*') ? 'active' : '' }}"
                                    href="{{ route('collections.index') }}">{{ __('navigation.collections') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('order') ? 'active' : '' }}"
                                    href="{{ route('orders.index') }}">{{ __('navigation.order') }}</a>
                            </li>

                            <!-- Language (Mobile) -->
                            <li class="dropdown d-block d-sm-none pe-2">
                                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                    <img id="flag-icon"
                                        src="https://flagcdn.com/w20/{{ app()->getLocale() == 'id' ? 'id' : 'gb' }}.png"
                                        alt="{{ strtoupper(app()->getLocale()) }}" class="rounded-circle"
                                        width="30" height="30" />
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form method="GET" action="{{ url()->current() }}">
                                            <input type="hidden" name="lang" value="id">
                                            <button type="submit" class="dropdown-item">
                                                Indonesia
                                                @if (app()->getLocale() == 'id')
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="GET" action="{{ url()->current() }}">
                                            <input type="hidden" name="lang" value="en">
                                            <button type="submit" class="dropdown-item">
                                                English
                                                @if (app()->getLocale() == 'en')
                                                @endif
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <hr style="border-top: 2px solid #341c02;">
                                    </li>
                                </ul>
                            </li>


                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button class="nav-link" type="submit" style="color: red">
                                        <i class="bi bi-box-arrow-right me-1"></i>{{ __('navigation.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Language (Desktop) --}}
                <div class="dropdown d-none d-sm-block pe-2">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <img id="flag-icon"
                            src="https://flagcdn.com/w20/{{ app()->getLocale() == 'id' ? 'id' : 'gb' }}.png"
                            alt="{{ strtoupper(app()->getLocale()) }}" class="rounded-circle" width="30"
                            height="30" />
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="GET" action="{{ url()->current() }}">
                                <input type="hidden" name="lang" value="id">
                                <button type="submit" class="dropdown-item">
                                    Indonesia
                                    @if (app()->getLocale() == 'id')
                                    @endif
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="GET" action="{{ url()->current() }}">
                                <input type="hidden" name="lang" value="en">
                                <button type="submit" class="dropdown-item">
                                    English
                                    @if (app()->getLocale() == 'en')
                                    @endif
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>


                {{-- Cart + Profile (Always on right) --}}
                <div class="d-flex align-items-center gap-2 ms-auto pe-2">
                    <a class="nav-link" href="{{ route('cart.index', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}">
                        <i class="bi bi-cart3 fs-2" style="color: #52282A;"></i>
                    </a>
                    {{-- PROFILE BUAT DESKTOP --}}
                    <div class="dropdown d-lg-block d-none ms-3">
                        <a href="" class="nav-link" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-2" style="color: #341c02;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item"
                                    href="{{ route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('cart.index', ['id_user'=>Auth::user()->id, 'slug'=>Str::slug(Auth::user()->name)]) }}">
                                    <i class="bi bi-box-arrow-right me-2"></i>Log out
                                </a>
                            </li>
                    </div>

                    <!-- DIPERBAIKI HREF & VISIBILITAS: Ikon Keranjang untuk Mobile -->
                    <a class="nav-link d-lg-none d-block" href="{{ route('cart.index', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}">
                        <i class="bi bi-cart3 fs-2" style="color: #52282A;"></i>
                    </a>

                    {{-- PROFILE BUAT MOBILE (posisinya tetap di dalam grup) --}}
                    <div class="d-block d-sm-none ms-3">
                        <a class="nav-link" href="#">
                            <i class="bi bi-person-circle fs-2" style="color: #341c02;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main class="content-box">
            @yield('content')
        </main>
    </div>
</body>

</html>
