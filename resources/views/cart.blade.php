@extends('layouts.app')

{{-- CSS khusus halaman ini, dimuat di <head> melalui @yield('style') di layouts/app.blade.php --}}
@section('style')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

{{-- JavaScript khusus halaman ini, dimuat di akhir <body> melalui @yield('script') di layouts/app.blade.php --}}
@section('script')
    <script>

        const CURRENT_USER_ID = @json(Auth::check() ? Auth::id() : null);

        // --- DEBUGGING: Log URL aset ke konsol (opsional, bisa dihapus setelah berhasil) ---
        console.log("DEBUGGING ASSET PATHS:");
        console.log("cart.css URL:", "{{ asset('css/cart.css') }}");
        console.log("address.css URL:", "{{ asset('css/address.css') }}");
        console.log("js/address.js URL:", "{{ asset('js/address.js') }}");
        console.log("js/cart.js URL:", "{{ asset('js/cart.js') }}");
        console.log("assets/arrow_back.png URL:", "{{ asset('assets/arrow_back.png') }}");
        console.log("assets/cny_Tower1.png URL:", "{{ asset('assets/cny_Tower1.png') }}");
        // --- END DEBUGGING ---

        // PERUBAHAN PENTING: Meneruskan data cartItems yang sudah diproses dari Controller
        const initialCartItems = @json($cartItems);
        console.log("DEBUG: Initial cart items from Blade:", initialCartItems);
    </script>
    <script src="{{ asset('js/address.js') }}"></script>
    <script src="{{ asset('js/cart.js') }}"></script> {{-- Pastikan cart.js dimuat setelah initialCartItems didefinisikan --}}
@endsection

@section('content')
<div class="container mt-5" >
    <div class="row">
        <div class="col-md-8">
            <div class="shipping-address-box-style-2 p-3 mb-3" style="background-color: #FFF8E2; border-radius: 10px; padding: 15px;">
                <h5 class="fw-bold" style="color: #52282A;">Shipping Address</h5>
                <div class="d-flex align-items-start mb-2" id="shippingAddressDisplayContainer">
                    <p class="mb-0" id="currentShippingAddress" style="color: #52282A;">
                        Cumi Cumi Pak Kris Ikan Bakar Sambal Matan, Jalan Pakuan No. 3 Kel. Babakan Madang, Kab. Bogor, Jawa Barat
                    </p>
                </div>
                <button id="ubahAlamatBtn" class="btn btn-sm" style="background-color: #FFF8E2; border: 1px solid #D1BB9E; color: #52282A; padding: 5px 15px; border-radius: 20px; font-weight: bold;">Ubah</button>
            </div>

            <div class="select-all d-flex justify-content-between align-items-center">
                <label class="mb-0">
                    <input type="checkbox" id="select-all"> Select All
                </label>

                <button id="remove-btn" class="btn btn-link text-decoration-none d-none">
                    Remove
                </button>
            </div>

            <div class="product-list mt-3" id="cart-product-list">
                @if(empty($cartItems)) {{-- Menggunakan empty() karena $cartItems sekarang array PHP --}}
                    <p>Keranjang Anda kosong.</p> {{-- Pesan default jika keranjang kosong --}}
                @else
                    @foreach($cartItems as $cartItem)
                        <div class="product-item d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="product-checkbox me-2"
                                    data-name="{{ $cartItem['name'] }}" data-price="{{ $cartItem['price'] }}">
                                {{-- PERBAIKAN: Menggunakan gambar dari koleksi produk --}}
                                <img src="{{ $cartItem['image'] }}" alt="{{ $cartItem['name'] }}" class="product-img">
                                <div class="ms-2">
                                    <h6 class="fw-bold mb-0">{{ $cartItem['name'] }}</h6>
                                    <small>{{ $cartItem['quantity'] }} pcs</small>
                                </div>
                            </div>
                            <p class="mb-0 me-3">Rp {{ number_format($cartItem['total_price'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                @endif
            </div>


        </div>

        <div class="col-md-4">
            <div class="summary-box p-3">
                <h5 class="fw-bold" style="color:#52282A;font-size:20px;">
                    Cart (<span id="product-count">0</span> Product)
                </h5>
                <hr style="border-top:2px solid #52282A;">

                <h6 class="mb-1">Shipping</h6>
                <div class="d-flex justify-content-between">
                    <span>REX Regular</span>
                    <span>Rp 9.500</span>
                </div>

                <h6 class="mt-3 mb-1">Products</h6>
                {{-- PERBAIKAN: Mengubah ID agar unik --}}
                <div id="summary-product-list"><em>No product selected</em></div>

                <hr style="border-top:2px solid #52282A;">

                <div class="d-flex justify-content: flex-end; align-items-center mb-3">
                    <h5 class="fw-bold" style="font-size:20px;">Total</h5>
                    <p id="total" class="fw-bold mb-0" style="font-size:20px;">Rp 9.500</p>
                </div>

                <hr style="border-top:2px solid #52282A;">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">Checkout</button>
                </div>
            </div>
        </div>
{{-- START: HTML Modals (Address & Add Form) - Disesuaikan untuk tampilan baru --}}
<div id="addressModal" class="modal" style="display: none; z-index: 1050;">
    {{-- Modal Content (Bagian yang di dalam border gelap) --}}
    <div class="modal-content" style="background-color: #FFFBEF; border-radius: 15px; padding: 0;">
        {{-- Header Modal Custom --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <h2 style="color: #52282A; margin: 0; font-size: 24px;">Pilih Alamat</h2>
            <span class="close-button" style="color: white; font-size: 30px; cursor: pointer;">&times;</span>
        </div>

        {{-- Body Modal (List Alamat dan Tombol Tambah) --}}
        <div style="padding: 20px;">
            <div id="addressList" class="address-list" style="margin-bottom: 15px;">
                <p style="color: #52282A;">Memuat alamat...</p>
            </div>

            {{-- Tombol Tambah Alamat Baru (disesuaikan seperti di gambar) --}}
            <button id="tambahAlamatBtn" class="btn" style="background-color: #FFF8E2; border-radius: 10px; color: #52282A; font-weight: bold; padding: 15px 20px; width: 100%; display: flex; justify-content: center; align-items: center; font-size: 18px; border: none; margin-bottom: 20px;">
                <i class="bi bi-plus-circle-fill me-2" style="font-size: 22px; color: #52282A;"></i>
                Tambah Alamat
            </button>

            {{-- START PERBAIKAN: Tombol "Pilih Alamat" sekarang di dalam modal-content, di kanan bawah --}}
            <div style="display: flex; justify-content: flex-end;">
                <button id="confirmAddressBtn" class="btn" style="background-color: #52282A; color: white; padding: 10px 20px; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; display: none;">Pilih Alamat</button>
            </div>
            {{-- END PERBAIKAN --}}
        </div>
    </div>
</div>

{{-- Modal Tambah Alamat (tetap sama, atau sesuaikan z-index jika masih tertutup navbar) --}}
<div id="addAddressFormModal" class="modal" style="display: none; z-index: 1050;">
    <div class="modal-content" style="background-color: #52282A; border-radius: 15px; padding: 0;">
        {{-- Header Modal Custom --}}
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <h2 style="color: white; margin: 0; font-size: 24px;">Tambah Alamat Baru</h2>
            <span class="close-button" style="color: #52282A; font-size: 30px; cursor: pointer;">&times;</span>
        </div>

        {{-- Body Modal Form --}}
        <div style="padding: 20px;">
            <form id="newAddressForm">
                @csrf
                <label for="receiver_name" style="color: white;">Nama Penerima:</label>
                <input type="text" id="receiver_name" name="receiver_name" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">
                <label for="phone_number" style="color: white;">Nomor Telepon:</label>
                <input type="text" id="phone_number" name="phone_number" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="label" style="color: white;">Label Alamat (ex: Rumah, Kantor):</label>
                <input type="text" id="label" name="label" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="address" style="color: white;">Alamat Lengkap (Jalan, No. Rumah, dll.):</label>
                <textarea id="address" name="address" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;"></textarea>

                <label for="provinsi" style="color: white;">Provinsi:</label>
                <input type="text" id="provinsi" name="provinsi" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="kota_kabupaten" style="color: white;">Kota/Kabupaten:</label>
                <input type="text" id="kota_kabupaten" name="kota_kabupaten" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="kecamatan" style="color: white;">Kecamatan:</label>
                <input type="text" id="kecamatan" name="kecamatan" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="kelurahan_desa" style="color: white;">Kelurahan/Desa:</label>
                <input type="text" id="kelurahan_desa" name="kelurahan_desa" required style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="rt" style="color: white;">RT:</label>
                <input type="text" id="rt" name="rt" maxlength="3" style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="rw" style="color: white;">RW:</label>
                <input type="text" id="rw" name="rw" maxlength="3" style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <label for="kode_pos" style="color: white;">Kode Pos:</label>
                <input type="text" id="kode_pos" name="kode_pos" required maxlength="10" style="width: calc(100% - 20px); padding: 8px; margin-top: 5px; border: 1px solid #D1BB9E; border-radius: 4px; background-color: #F8F0E3; color: #52282A;">

                <div class="checkbox-group" style="margin-top: 15px; display: flex; align-items: center;">
                    <input type="checkbox" id="is_primary" name="is_primary" value="1" style="margin-right: 8px;">
                    <label for="is_primary" style="color: white;">Jadikan Alamat Utama</label>
                </div>

                <div class="modal-actions" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn success-btn" style="background-color: #28a745; color: white; padding: 8px 15px; border-radius: 5px; cursor: pointer; border: none;">Simpan Alamat</button>
                    <button type="button" class="btn secondary-btn close-add-form-button" style="background-color: #6c757d; color: white; padding: 8px 15px; border-radius: 5px; cursor: pointer; border: none;">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END: HTML Modals --}}

</div> {{-- Penutup div container --}}
@endsection
