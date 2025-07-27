<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Coba</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body style="background-color: #fffbec">
    <nav class="navbar fixed-top navbar-expand-sm navbar-light" style="height: 70px">
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
                    <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" width="55" height="42"
                        style="display: block; position: relative; top:7px" />
                </div>
            </div>

            {{-- Desktop Logo --}}
            <a class="navbar-brand d-none d-sm-block m-4 p-2" href="#">
                <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" width="65" height="50"
                    class="d-inline-block align-text-top" />
            </a>

            <!-- Menu NAVBAR Desktop -->
            <div class="collapse navbar-collapse d-none d-sm-flex" id="collapsibleNavId">
                <ul class="navbar-nav fs-5 flex-row gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">Home</a>
                    </li>
                    {{-- <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('custom') ? 'active' : '' }}"
                                href="{{ route('custom') }}">Custom</a>
                        </li> --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('collections.index') ? 'active' : '' }}"
                            href="{{ route('collections.index') }}">Collections</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs() ? 'active' : '' }}" href="">Order</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                            href="{{ route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}">Profile</a>
                    </li>
                </ul>
            </div>

            {{-- Burger Content --}}
            <div class="offcanvas offcanvas-start sidebar d-block d-sm-none" tabindex="-1" id="offcanvasNav"
                aria-labelledby="offcanvasNavLabel" style="width: 50%; background-color: #fdc307;">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">

                    <!-- Logo (mobile) dalem burger -->
                    <div class="logo-burger d-block d-sm-none mb-3 ">
                        <img src="{{ asset('assets/logo/logo.png') }}" alt="Logo" width="60" height="45" />
                    </div>

                    <!-- Menu -->
                    <ul class="navbar-nav fs-5 flex-column gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}"
                                href="homepage">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('custom') ? 'active' : '' }}"
                                href="custom">Custom</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('collections*') ? 'active' : '' }}"
                                href="collections">Collections</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('order') ? 'active' : '' }}"
                                href="order">Order</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}"
                                href="{{ route('profile', ['id' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}">Profile</a>
                        </li>

                        <!-- Language (Mobile) -->
                        <li class="nav-item dropdown d-block d-sm-none">

                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Language
                            </a>

                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="setLanguage('id')">Indonesia</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="setLanguage('eng')">English</a>
                                </li>
                            </ul>
                            <hr style="border-top: 2px solid #341c02;">
                        </li>

                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link" style="color: red">
                                    <i class="bi bi-box-arrow-right me-1"></i>Log out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Language (Desktop)
                <div class="dropdown d-none d-sm-block pe-2">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <img id="flag-icon" src="https://flagcdn.com/w20/id.png" alt="ID"
                            class="rounded-circle" width="30" height="30" />
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="setLanguage('id')">Indonesia</a></li>
                        <li><a class="dropdown-item" href="#" onclick="setLanguage('eng')">English</a></li>
                    </ul>
                </div> --}}

            {{-- Cart + Profile (Always on right) --}}
            <div class="d-flex align-items-center gap-2 ms-auto pe-2">
                <!-- BARU DITAMBAHKAN / DIPINDAHKAN: Ikon Keranjang untuk Desktop -->
                <a class="nav-link d-none d-sm-block" href="{{ route('cart') }}">
                    <i class="bi bi-cart3 fs-1" style="color: #341c02;"></i>
                </a>

                <!-- DIPINDAHKAN: Dropdown Bahasa untuk Desktop -->
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

                {{-- PROFILE BUAT DESKTOP (posisinya tetap di dalam grup, setelah bahasa) --}}
                <div class="dropdown d-none d-sm-block ms-3">
                    <!-- ... kode profil desktop ... -->
                </div>

                <!-- DIPERBAIKI HREF & VISIBILITAS: Ikon Keranjang untuk Mobile -->
                <a class="nav-link d-block d-sm-none" href="{{ route('cart') }}">
                    <i class="bi bi-cart3 fs-2" style="color: #52282A;"></i>
                </a>

                {{-- PROFILE BUAT MOBILE (posisinya tetap di dalam grup) --}}
                <div class="d-block d-sm-none ms-3">
                    <!-- ... kode profil mobile ... -->
                </div>
            </div>


        </div>
    </nav>

    <div class="modal fade" id="editBottomModal" tabindex="-1" aria-labelledby="editBottomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('user.update', Auth::user()->id) }}" method="POST"
                class="modal-content modal-1-dalem">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBottomModalLabel">{{ __('profile.editInfo') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">{{ __('profile.gender') }}</label>
                            <select name="gender" class="form-select" id="gender">
                                <option value="Laki-laki" {{ Auth::user()->gender == 'Laki-laki' ? 'selected' : '' }}>
                                    {{ __('profile.male') }}
                                </option>
                                <option value="Perempuan" {{ Auth::user()->gender == 'Perempuan' ? 'selected' : '' }}>
                                    {{ __('profile.female') }}
                                </option>
                            </select>

                        </div>
                        <div class="mb-3">
                            <label for="dateofbirth" class="form-label">{{ __('profile.dateOfBirth') }}</label>
                            <input value="{{ Auth::user()->date_of_birth }}" type="date" class="form-control"
                                name="dateofbirth" id="dateofbirth" placeholder="{{ __('profile.inputDOB') }}">
                        </div>
                        <div class="mb-3">
                            <label for="email2" class="form-label">{{ __('profile.email') }}</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="form-control"
                                name="email" id="email2" placeholder="{{ __('profile.inputEmail') }}">
                        </div>
                        <div class="mb-3">
                            <label for="telepon" class="form-label">{{ __('profile.phoneNumber') }}</label>
                            <input type="number" value="{{ Auth::user()->phone_number }}" name="telepon"
                                class="form-control" id="telepon" placeholder="{{ __('profile.inputPhoneNumber') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">{{ __('profile.close') }}</button>
                    <button type="submit" class="btn btn-simpan">{{ __('profile.saveChanges') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('address.store') }}" method="POST" class="modal-content modal-1-dalem">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">{{ __('profile.editInfo') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('profile.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="label" class="form-label">{{ __('profile.label') }}</label>
                        <input type="text" value="" name="label" class="form-control" id="label"
                            placeholder="{{ __('profile.placeHolderAddress') }}">
                    </div>
                    <div class="mb-3">
                        <label for="provinsi" class="form-label">{{ __('profile.province') }}</label>
                        <input type="text" value="" name="provinsi" class="form-control" id="provinsi"
                            placeholder="{{ __('profile.inputProvince') }}">
                    </div>
                    <div class="mb-3">
                        <label for="kota_kabupaten" class="form-label">{{ __('profile.city') }}</label>
                        <input type="text" value="" name="kota_kabupaten" class="form-control"
                            id="kota_kabupaten" placeholder="{{ __('profile.inputCity') }}">
                    </div>
                    <div class="mb-3">
                        <label for="kecamatan" class="form-label">{{ __('profile.district') }}</label>
                        <input type="text" value="" name="kecamatan" class="form-control" id="kecamatan"
                            placeholder="{{ __('profile.inputDistrict') }}">
                    </div>
                    <div class="mb-3">
                        <label for="kelurahan_desa" class="form-label">{{ __('profile.village') }}</label>
                        <input type="text" value="" name="kelurahan_desa" class="form-control"
                            id="kelurahan_desa" placeholder="{{ __('profile.inputVillage') }}">
                    </div>
                    <div class="mb-3">
                        <label for="rt" class="form-label">{{ __('profile.rt') }}</label>
                        <input type="text" value="" name="rt" class="form-control" id="rt"
                            placeholder="{{ __('profile.inputRT') }}">
                    </div>
                    <div class="mb-3">
                        <label for="rw" class="form-label">{{ __('profile.rw') }}</label>
                        <input type="text" value="" name="rw" class="form-control" id="rw"
                            placeholder="{{ __('profile.inputRW') }}">
                    </div>
                    <div class="mb-3">
                        <label for="kode_pos" class="form-label">{{ __('profile.postalCode') }}</label>
                        <input type="text" value="" name="kode_pos" class="form-control" id="kode_pos"
                            placeholder="{{ __('profile.inputPostalCode') }}">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">{{ __('profile.fullAddress') }}</label>
                        <textarea name="address" class="form-control" id="address" placeholder="{{ __('profile.inputFullAddress') }}"
                            rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">{{ __('profile.close') }}</button>
                    <button type="submit" class="btn btn-simpan">{{ __('profile.saveChanges') }}</button>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('user.update', Auth::user()->id) }}" method="POST"
                class="modal-content modal-1-dalem" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">{{ __('profile.editUserInfo') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('profile.enterForm') }}</p>
                    <div>
                        <div class="mb-3">
                            <label for="namaUser" class="form-label">{{ __('profile.userName') }}</label>
                            <input name="name" value="{{ Auth::user()->name }}" type="text"
                                class="form-control" id="namaUser" placeholder="{{ __('profile.inputUserName') }}">
                        </div>
                        <div class="mb-3"> {{-- Add this new div for image upload --}}
                            <label for="imageUser" class="form-label">{{ __('profile.profileImage') }}</label>
                            <input name="profile_image" type="file" class="form-control" id="imageUser"
                                accept="image/*">
                            <small class="form-text text-muted">{{ __('profile.chooseImage') }}</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">{{ __('profile.close') }}</button>
                    <button type="submit" class="btn btn-simpan">{{ __('profile.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div id="container-luar">
        <div class="d-flex mt-5 py-5" id="container-luar-2">
            <div class="container d-flex flex-column justify-content-around shadow p-3 mb-5 rounded" id="container1">
                <div class="container d-flex flex-column justify-content-center align-items-center p-3">
                    <h2 class="side-link-2">{{ Auth::user()->name }}</h2>
                    <hr class="garis" />
                </div>
                <div class="container d-flex flex-column mb-5 align-items-center">
                    <a href="#" class="side-link side-link-2 p-2 active" data-slide-to="0">{{ __('profile.userInfo') }}</a>
                    <a href="#" class="side-link side-link-2 p-2 mt-1" data-slide-to="1">{{ __('profile.address') }}</a>
                    <a href="#" class="side-link side-link-2 p-2 mt-1" data-slide-to="2">{{ __('profile.faq') }}</a>
                    <hr class="garis" />
                </div>
                <div class="container d-flex justify-content-center align-items-center">
                    <a href="/login" class="side-link side-link-2 p-2 mb-4">{{ __('profile.logout') }}</a>
                </div>
            </div>


            <div class="container d-block shadow p-3 mb-5 rounded" id="container2">
                <div id="carousel1" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        <!-- Slide 1: User Info -->
                        <div class="carousel-item active">
                            <div class="container d-flex p-3 flex-row">
                                <div class="container d-flex flex-column">
                                    <div class="d-flex flex-row align-items-center p-3" id="atas-profile">
                                        <img src="{{asset('assets/profile/' . (Auth::user()->profile_image ?? 'orang2.jpg')) }}" alt="orang"
                                            class="gambar-profile rounded-circle" />
                                        <div class="d-flex flex-column p-3 flex-grow-1 nama_badge">
                                            <h1 class="nama-user mb-1">{{ Auth::user()->name }}</h1>
                                            <div
                                                class="info-user-atas badge text-bg-primary">{{ Auth::user()->role }}</div>
                                        </div>
                                        <div class="ms-auto align-self-start" data-bs-toggle="modal"
                                            data-bs-target="#editModal">
                                            <button class="button-edit">✏️ {{ __('profile.edit') }}</button>
                                        </div>
                                    </div>
                                    <hr class="garis" />
                                    <div class="d-flex flex-column align-items-start p-3" id="info-container">
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">{{ __('profile.gender') }}</h4>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">{{ Auth::user()->gender }}</h4>
                                            </div>
                                        </div>
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">{{ __('profile.age') }}</h4>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">
                                                    {{ substr(\Carbon\Carbon::parse(Auth::user()->date_of_birth)->diffForHumans(), 0, 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">{{ __('profile.email') }}</h4>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">{{ Auth::user()->email }}</h4>
                                            </div>
                                        </div>
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <div class="no-telepon">
                                                    <h4 class="judul-info-user">{{ __('profile.phoneNumber') }}</h4>
                                                </div>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">{{ Auth::user()->phone_number }}</h4>
                                            </div>
                                        </div>
                                        {{-- <div class="d-flex flex-row">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">Gender</h4>
                                                <h4 class="judul-info-user mt-4">Age</h4>
                                                <h4 class="judul-info-user mt-4">Email</h4>
                                                <div class="no-telepon">
                                                    <h4 class="judul-info-user mt-4">Telephone Number</h4>
                                                </div>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">{{ Auth::user()->gender }}</h4>
                                                <h4 class="info-user mt-4">
                                                    {{ substr(\Carbon\Carbon::parse(Auth::user()->date_of_birth)->diffForHumans(), 0, 2) }}
                                                </h4>
                                                <div>
                                                    <h4 class="info-user mt-4">{{ Auth::user()->email }}</h4>
                                                </div>
                                                <h4 class="info-user mt-4">{{ Auth::user()->phone_number }}</h4>

                                            </div>
                                        </div> --}}
                                        <div class="ms-auto align-self-start" id="edit-bawah">
                                            <button class="button-edit" data-bs-toggle="modal"
                                                data-bs-target="#editBottomModal">✏️ {{ __('profile.edit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Address -->
                        <div class="carousel-item">
                            <div class="container">
                                <div class="p-2 ms-2">
                                    <h2>{{ __('profile.address') }}</h2>
                                    <div>
                                        <div class="form-inline my-2 my-lg-0">
                                            <div class="d-flex flex-row justify-content-between">
                                                <button type="button" class="btn bg-warning" data-bs-toggle="modal"
                                                    data-bs-target="#addAddressModal">
                                                    + {{ __('profile.addAddress') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3">
                                    @forelse ($address as $a)
                                        <div class="container container-address mt-2 p-2">
                                            <div>
                                                <h5>{{ $a->label }}</h5>
                                                <p>{{ $a->address }}, {{ $a->rt }}/{{ $a->rw }},
                                                    {{ $a->kelurahan_desa }}, {{ $a->kecamatan }},
                                                    {{ $a->kota_kabupaten }}, {{ $a->provinsi }}</p>
                                            </div>
                                            <form action="{{ route('address.destroy', $a->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    {{ __('profile.delete') }}
                                                </button>
                                            </form>
                                        </div>

                                    @empty
                                        <div class="container mt-2">
                                            <h4>{{ __('profile.addressEmpty') }}</h4>
                                        </div>
                                    @endforelse
                                </div>

                            </div>
                        </div>

                        <!-- Slide 3: Notification -->
                        <div class="carousel-item">
                            <div class="container luar-faq">
                                <div class="container p-2 ms-2">
                                    <h2>{{ __('profile.frequentlyAskedQuestions') }}</h2>
                                </div>
                                <div class="accordion p-3" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="false"
                                                aria-controls="collapseOne">
                                                {{ __('profile.whatIsSnackTower') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{ __('profile.whatIsSnackTowerDescription') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseTwo" aria-expanded="false"
                                                aria-controls="collapseTwo">
                                               {{ __('profile.howICustomizeSnackTower') }}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {!! __('profile.howICustomizeSnackTowerDescription') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseThree" aria-expanded="false"
                                                aria-controls="collapseThree">
                                                {{ __('profile.doYouOffer')}}
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{ __('profile.doYouOfferDescription') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseFour" aria-expanded="false"
                                                aria-controls="collapseFour">
                                                {{__('profile.isThereAMinimumOrder')}}
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{__('profile.isThereAMinimumOrderDescription')}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseFive" aria-expanded="false"
                                                aria-controls="collapseFive">
                                                {{__('profile.whatIsDifference')}}
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {!!__('profile.whatIsDifferenceDescription')!!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseSix" aria-expanded="false"
                                                aria-controls="collapseSix">
                                               {{__('profile.whatIsMystery')}}
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                               {{__('profile.whatIsMysteryDescription')}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button
                                                class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseSeven" aria-expanded="false"
                                                aria-controls="collapseSeven">
                                                {{__('profile.canIChoose')}}
                                            </button>
                                        </h2>
                                        <div id="collapseSeven" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{__('profile.canIChooseDescription')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const carouselElement = document.querySelector('#carousel1');
        const carousel = new bootstrap.Carousel(carouselElement, {
            interval: false,
            ride: false
        });

        const menuLinks = document.querySelectorAll('[data-slide-to]');

        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const index = parseInt(this.getAttribute('data-slide-to'));
                carousel.to(index);

                menuLinks.forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    <script>
        const sidebar = document.getElementById('container1');
        const toggleButton = document.getElementById('toggleSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('active');
        });

        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('active');
        });
    </script>

</body>

</html>
