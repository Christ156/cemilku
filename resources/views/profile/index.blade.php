@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

{{-- @section('script')
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection --}}

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
            <h5>Gagal menyimpan Alamat! Mohon periksa kesalahan berikut:</h5>
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
                    <h5 class="modal-title" id="editBottomModalLabel">Edit Info Lengkap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" class="form-select" id="gender">
                                <option value="Laki-laki" {{ Auth::user()->gender == 'Laki-laki' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="Perempuan" {{ Auth::user()->gender == 'Perempuan' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>

                        </div>
                        <div class="mb-3">
                            <label for="dateofbirth" class="form-label">Date of Birth</label>
                            <input value="{{ Auth::user()->date_of_birth }}" type="date" class="form-control"
                                name="dateofbirth" id="dateofbirth" placeholder="Masukkan Date of Birth">
                        </div>
                        <div class="mb-3">
                            <label for="email2" class="form-label">Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="form-control" name="email"
                                id="email2" placeholder="Masukkan email">
                        </div>
                        <div class="mb-3">
                            <label for="telepon" class="form-label">No. Telepon</label>
                            <input type="number" value="{{ Auth::user()->phone_number }}" name="telepon"
                                class="form-control" id="telepon" placeholder="Masukkan no. telepon">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-simpan">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('address.store') }}" method="POST" class="modal-content modal-1-dalem">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editAddressModalLabel">Edit Info Lengkap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="label" class="form-label">Label</label>
                        <input type="text" value="" name="label" class="form-control" id="label"
                            placeholder="Contoh: Alamat Rumah / Kantor">
                    </div>
                    <div class="mb-3">
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <input type="text" value="" name="provinsi" class="form-control" id="provinsi"
                            placeholder="Masukkan Provinsi...">
                    </div>
                    <div class="mb-3">
                        <label for="kota_kabupaten" class="form-label">Kota/Kabupaten</label>
                        <input type="text" value="" name="kota_kabupaten" class="form-control"
                            id="kota_kabupaten" placeholder="Masukkan Kota atau Kabupaten...">
                    </div>
                    <div class="mb-3">
                        <label for="kecamatan" class="form-label">Kecamatan</label>
                        <input type="text" value="" name="kecamatan" class="form-control" id="kecamatan"
                            placeholder="Masukkan Kecamatan...">
                    </div>
                    <div class="mb-3">
                        <label for="kelurahan_desa" class="form-label">Kelurahan/Desa</label>
                        <input type="text" value="" name="kelurahan_desa" class="form-control"
                            id="kelurahan_desa" placeholder="Masukkan Kelurahan atau Desa...">
                    </div>
                    <div class="mb-3">
                        <label for="rt" class="form-label">RT</label>
                        <input type="text" value="" name="rt" class="form-control" id="rt"
                            placeholder="Masukkan RT...">
                    </div>
                    <div class="mb-3">
                        <label for="rw" class="form-label">RW</label>
                        <input type="text" value="" name="rw" class="form-control" id="rw"
                            placeholder="Masukkan RW...">
                    </div>
                    <div class="mb-3">
                        <label for="kode_pos" class="form-label">Kode Pos</label>
                        <input type="text" value="" name="kode_pos" class="form-control" id="kode_pos"
                            placeholder="Masukkan Kode Pos...">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea name="address" class="form-control" id="address" placeholder="Masukkan Alamat Lengkap..."
                            rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                        <input type="number" value="" name="nomor_telepon" class="form-control"
                            id="nomor_telepon" placeholder="Masukkan Nomor Telepon...">
                    </div>
                    <div class="mb-3">
                        <label for="receiver_name" class="form-label">Nama Penerima</label>
                        <input type="text" value="" name="receiver_name" class="form-control"
                            id="receiver_name" placeholder="Masukkan Nama Penerima...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-simpan">Simpan Perubahan</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit User Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Masukkan konten formulir atau informasi untuk edit di sini.</p>
                    <div>
                        <div class="mb-3">
                            <label for="namaUser" class="form-label">Nama User</label>
                            <input name="name" value="{{ Auth::user()->name }}" type="text" class="form-control"
                                id="namaUser" placeholder="Masukkan nama...">
                        </div>
                        <div class="mb-3"> {{-- Add this new div for image upload --}}
                            <label for="imageUser" class="form-label">Profile Image</label>
                            <input name="profile_image" type="file" class="form-control" id="imageUser"
                                accept="image/*">
                            <small class="form-text text-muted">Choose an image from your device.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-simpan">Simpan</button>
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
                    <a href="#" class="text-center side-link side-link-2 p-2 active" data-slide-to="0">User
                        Info</a>
                    <a href="#" class="text-center side-link side-link-2 p-2 mt-1" data-slide-to="1">Address</a>
                    <a href="#" class="text-center side-link side-link-2 p-2 mt-1" data-slide-to="2">FAQ</a>
                    <hr class="garis" />
                </div>
                <form action="{{ route('logout') }}" class="container d-flex justify-content-center align-items-center"
                    method="POST">
                    @csrf
                    <button type="submit" class="side-link side-link-2 p-2 mb-4">Log Out</a>
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
                                            <button class="button-edit">✏️ Edit</button>
                                        </div>
                                    </div>
                                    <hr class="garis" />
                                    <div class="d-flex flex-column align-items-start p-3" id="info-container">
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">Gender</h4>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">{{ Auth::user()->gender }}</h4>
                                            </div>
                                        </div>
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">Age</h4>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">
                                                    {{ substr(\Carbon\Carbon::parse(Auth::user()->date_of_birth)->diffForHumans(), 0, 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <h4 class="judul-info-user">Email</h4>
                                            </div>
                                            <div class="container-info-user">
                                                <h4 class="info-user">{{ Auth::user()->email }}</h4>
                                            </div>
                                        </div>
                                        <div class="container info-luar">
                                            <div class="container-judul-info-user">
                                                <div class="no-telepon">
                                                    <h4 class="judul-info-user">Telephone Number</h4>
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
                                                data-bs-target="#editBottomModal">✏️ Edit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2: Address -->
                        <div class="carousel-item">
                            <div class="container">
                                <div class="p-2 ms-2">
                                    <h2>Address</h2>
                                    <div>
                                        <div class="form-inline my-2 my-lg-0">
                                            <div class="d-flex flex-row justify-content-between">
                                                <button type="button" class="btn bg-warning" data-bs-toggle="modal"
                                                    data-bs-target="#addAddressModal">
                                                    + Add Address
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
                                                <p>Receiver Name: {{ $a->receiver_name }}</p>
                                                <p>Description: {{ $a->address }},
                                                    {{ $a->rt }}/{{ $a->rw }},
                                                    {{ $a->kelurahan_desa }}, {{ $a->kecamatan }},
                                                    {{ $a->kota_kabupaten }}, {{ $a->provinsi }}</p>
                                                <p>Phone Number: {{ $a->phone_number }}</p>
                                            </div>
                                            <div class="d-flex flex-row justify-content-between">
                                                {{-- Delete Form --}}
                                                <form action="{{ route('address.destroy', $a->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        Delete
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
                                            <h4>Address is empty...</h4>
                                        </div>
                                    @endforelse
                                </div>

                            </div>
                        </div>

                        <!-- Slide 3: Notification -->
                        <div class="carousel-item">
                            <div class="container luar-faq">
                                <div class="container p-2 ms-2">
                                    <h2>Frequently Asked Questions</h2>
                                </div>
                                <div class="accordion p-3" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="false" aria-controls="collapseOne">
                                                What is a Snack Tower?
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                A Snack Tower is a customizable gift made up of stacked boxes filled
                                                with your chosen snacks. You can mix and match treats, packaging,
                                                and
                                                gift messages for any occasion.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                How do I customize my Snack Tower?
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                Tap the “Customize Snack Tower” button on the home screen. You’ll be
                                                guided
                                                through steps to:
                                                <br>
                                                1. Select the number of level (2-4)
                                                <br>
                                                2. Choose snacks for each tier
                                                <br>
                                                3. Choose decorations for snack tower (optional)
                                                <br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                                Do you offer pre-made towers?
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                Yes! Visit the Collections section to choose from popular designs
                                                with
                                                pre-selected snacks or event-based snack tower.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                aria-expanded="false" aria-controls="collapseFour">
                                                Is there a minimum order amount?
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                No minimum! You can build a tower with as few as 2 tiers. Pricing
                                                updates in real time as you customize.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                What is the difference between a Snack Tower and a Snack Bouquet?
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                - Snack Tower: A multi-tiered stack of boxes filled with your
                                                selected
                                                snacks. It’s presented in a vertical, gift-box format and often
                                                themed
                                                by occasion or color.
                                                <br>
                                                - Snack Bouquet: A visually striking arrangement where snacks are
                                                wrapped
                                                or skewered like flowers in a bouquet, often in a decorative
                                                container
                                                or wrapper.
                                                <br>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                aria-expanded="false" aria-controls="collapseSix">
                                                What is a Mystery Snack Box?
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                A Mystery Snack Box is a surprise selection of snacks chosen by the
                                                seller, based on your selected budget, mood, and preference. You
                                                won’t
                                                know exactly what’s inside until it arrives!
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mt-3">
                                        <h2 class="accordion-header">
                                            <button class="bg-warning accordion-button collapsed tulisan-judul-faq fw-bold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven"
                                                aria-expanded="false" aria-controls="collapseSeven">
                                                Can I choose the Mystery Box's snacks by myself?
                                            </button>
                                        </h2>
                                        <div id="collapseSeven" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                No. Mystery Boxes are intentionally randomized to keep the surprise
                                                factor. If you want to build your own, check out our Snack Tower or
                                                Bouquet options.
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
@endsection
