@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>
    {{-- <script src="{{ asset('js/profile.js') }}"></script> --}}
@endsection


@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Ini untuk menampilkan pesan error validasi spesifik per field --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5>{{ __('profile.failedToSave') }}</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="modal fade" id="editBottomModal" tabindex="-1" aria-labelledby="editBottomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('user.update', Auth::user()->id) }}" method="POST" class="modal-content modal-1-dalem">
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
                            <label for="email2" class="form-label">Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="form-control" name="email"
                                id="email2" placeholder="Masukkan email">
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

    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('address.store') }}" method="POST" class="modal-content modal-1-dalem">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">{{ __('profile.editInfo') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('profile.close') }}"></button>
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
                    <div class="mb-3">
                        <label for="nomor_telepon" class="form-label">{{ __('profile.phoneNumber') }}</label>
                        <input type="number" value="" name="nomor_telepon" class="form-control"
                            id="nomor_telepon" placeholder="{{ __('profile.inputPhoneNumber') }}">
                    </div>
                    <div class="mb-3">
                        <label for="receiver_name" class="form-label">{{ __('profile.reciepentName') }}</label>
                        <input type="text" value="" name="receiver_name" class="form-control"
                            id="receiver_name" placeholder="{{ __('profile.inputReciepentName') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup"
                        data-bs-dismiss="modal">{{ __('profile.close') }}</button>
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
                            <input name="name" value="{{ Auth::user()->name }}" type="text" class="form-control"
                                id="namaUser" placeholder="{{ __('profile.inputUserName') }}">
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
                    <button type="button" class="btn btn-tutup"
                        data-bs-dismiss="modal">{{ __('profile.close') }}</button>
                    <button type="submit" class="btn btn-simpan">{{ __('profile.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div id="container-luar">
        <div class="d-flex mt-5" id="container-luar-2">
            <div class="container d-flex flex-column justify-content-around shadow p-3 mb-5 rounded" id="container1">
                <div class="container d-flex flex-column justify-content-center align-items-center p-3">
                    <h2 class="side-link-2">{{ Auth::user()->name }}</h2>
                    <hr class="garis" />
                </div>
                <div class="container d-flex flex-column mb-5 justify-content-center align-items-center">
                    <a href="#" class="text-center side-link side-link-2 p-2 active"
                        data-slide-to="0">{{ __('profile.userInfo') }}</a>
                    <a href="#" class="text-center side-link side-link-2 p-2 mt-1"
                        data-slide-to="1">{{ __('profile.address') }}</a>
                    <a href="#" class="text-center side-link side-link-2 p-2 mt-1"
                        data-slide-to="2">{{ __('profile.faq') }}</a>
                    <hr class="garis" />
                </div>
                <form action="{{ route('logout') }}" class="container d-flex justify-content-center align-items-center"
                    method="POST">
                    @csrf
                    <button type="submit" class="side-link side-link-2 p-2 mb-4">{{ __('profile.logout') }}</a>
                </form>
            </div>


            <div class="container d-block shadow p-3 mb-5 rounded" id="container2">
                <div id="carousel1" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner">
                        <!-- Slide 1: User Info -->
                        <div class="carousel-item active">
                            <div class="container d-flex p-3 flex-row">
                                <div class="container d-flex flex-column">
                                    <div class="d-flex flex-row align-items-center p-3" id="atas-profile">
                                        <img src="{{ asset('assets/profile/' . (Auth::user()->profile_image ?? 'orang2.jpg')) }}"
                                            alt="orang" class="gambar-profile rounded-circle" />
                                        <div class="d-flex flex-column p-3 flex-grow-1 nama_badge">
                                            <h1 class="nama-user mb-1">{{ Auth::user()->name }}</h1>
                                            <div class="info-user-atas badge text-bg-primary">{{ Auth::user()->role }}
                                            </div>
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
                                                    {{-- {{ substr(\Carbon\Carbon::parse(Auth::user()->date_of_birth)->diffForHumans(), 0, 2) }} --}}
                                                    {{ Auth::user()->date_of_birth ? \Carbon\Carbon::parse(Auth::user()->date_of_birth)->age . ' years' : '-' }}
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
                                                <div class="d-flex flex-row justify-content-between">
                                                    <h5 class="label-address">{{ $a->label }}</h5>
                                                    <span id="status-text-{{ $a->id }}"
                                                        class="badge {{ $a->is_primary ? 'bg-primary' : 'bg-secondary' }} badge-address">
                                                        {{ $a->is_primary ? 'Utama' : 'Non-Utama' }}
                                                    </span>
                                                </div>
                                                <p>{{ __('profile.reciepentName') }}: {{ $a->receiver_name }}</p>
                                                <p>{{ __('profile.description') }}: {{ $a->address }},
                                                    {{ $a->rt }}/{{ $a->rw }},
                                                    {{ $a->kelurahan_desa }}, {{ $a->kecamatan }},
                                                    {{ $a->kota_kabupaten }}, {{ $a->provinsi }}</p>
                                                <p>{{ __('profile.phoneNumber') }}: {{ $a->phone_number }}</p>
                                            </div>
                                            <div class="d-flex flex-row justify-content-between">
                                                {{-- Delete Form --}}
                                                <form action="{{ route('address.destroy', $a->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        {{ __('profile.delete') }}
                                                    </button>
                                                </form>
                                                @if (!$a->is_primary)
                                                    <button type="button"
                                                        class="btn btn-sm toggle-primary-btn {{ $a->is_primary ? 'btn-secondary' : 'btn-primary' }}"
                                                        data-address-id="{{ $a->id }}"
                                                        data-current-primary="{{ $a->is_primary ? 'primary' : 'not-primary' }}"
                                                        {{ $a->is_primary ? 'disabled' : '' }}>
                                                        {{ $a->is_primary ? 'Utama Saat Ini' : 'Jadikan Utama' }}
                                                    </button>
                                                @endif
                                            </div>
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
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="false" aria-controls="collapseOne">
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
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
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
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                                {{ __('profile.doYouOffer') }}
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
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                aria-expanded="false" aria-controls="collapseFour">
                                                {{ __('profile.isThereAMinimumOrder') }}
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{ __('profile.isThereAMinimumOrderDescription') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                {{ __('profile.whatIsDifference') }}
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {!! __('profile.whatIsDifferenceDescription') !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                aria-expanded="false" aria-controls="collapseSix">
                                                {{ __('profile.whatIsMystery') }}
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{ __('profile.whatIsMysteryDescription') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven"
                                                aria-expanded="false" aria-controls="collapseSeven">
                                                {{ __('profile.canIChoose') }}
                                            </button>
                                        </h2>
                                        <div id="collapseSeven" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{ __('profile.canIChooseDescription') }}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Dapatkan semua tombol dengan kelas 'toggle-primary-btn'
            const toggleButtons = document.querySelectorAll('.toggle-primary-btn');
            const statusMessageDiv = document.getElementById('statusMessage');

            // 2. Tambahkan event listener untuk setiap tombol
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // 3. Ambil ID alamat dari atribut data-address-id tombol yang diklik
                    const addressId = this.dataset.addressId;
                    const newPrimaryStatus = 'primary'; // Kita selalu ingin menjadikannya primary

                    // ... (kode untuk menonaktifkan tombol dan update UI sementara) ...

                    // 4. Kirim permintaan AJAX ke backend Laravel
                    fetch(`/addresses/${addressId}/toggle-primary`, { // <<<--- INI MEMANGGIL CONTROLLER!
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                status: newPrimaryStatus
                            })
                        })
                        .then(response => {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);

                        })
                        .catch(error => {
                            // ... (tangani error) ...
                        });
                });
            });

            // ... (fungsi displayStatusMessage) ...

        });
    </script>
    <script>
        const carouselElement = document.querySelector('#carousel1');
        const carousel = new bootstrap.Carousel(carouselElement, {
            interval: false,
            ride: false
        });

        const menuLinks = document.querySelectorAll('[data-slide-to]');

        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // e.preventDefault();

                const index = parseInt(this.getAttribute('data-slide-to'));
                carousel.to(index);

                menuLinks.forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });

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
@endsection

