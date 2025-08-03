console.log("languange_swithcer.js berhasil dimuat dan dieksekusi!");

// --- FUNGSI-FUNGSI PEMBANTU (HELPER FUNCTIONS) DIDEFINISIKAN DI SINI ---
let addressModal;
let addAddressFormModal;
let addressList;
let tambahAlamatBtn;
let newAddressForm;
let currentShippingAddressDiv;
let csrfToken;
let confirmAddressBtn;

let selectedAddressIdForConfirmation = null; // Menyimpan ID alamat yang sedang dipilih di modal

// Fungsi untuk membuka modal (pop-up)
function openModal(modalElement) {
    console.log("DEBUG: openModal dipanggil untuk:", modalElement.id);
    modalElement.style.display = 'flex'; // Mengubah display menjadi flex untuk menampilkan modal
    document.body.style.overflow = 'hidden'; // Mencegah scrolling body saat modal terbuka

    // Jika modal alamat yang dibuka, sembunyikan tombol konfirmasi awal
    if (modalElement === addressModal) {
        if (confirmAddressBtn) {
            confirmAddressBtn.style.display = 'none';
            console.log("DEBUG: confirmAddressBtn disembunyikan saat modal dibuka.");
        }
    }
}

// Fungsi untuk menutup modal (pop-up)
function closeModal(modalElement) {
    console.log("DEBUG: closeModal dipanggil untuk:", modalElement.id);
    modalElement.style.display = 'none'; // Menyembunyikan modal

    // Mengembalikan scrolling body jika tidak ada modal lain yang terbuka
    if (addressModal.style.display === 'none' && addAddressFormModal.style.display === 'none') {
        document.body.style.overflow = 'auto';
    }

    // Sembunyikan tombol konfirmasi dan reset pilihan alamat saat modal ditutup
    // PERBAIKAN BUG: Mengubah 'confirmConfirmAddressBtn' menjadi 'confirmAddressBtn'
    if (confirmAddressBtn) confirmAddressBtn.style.display = 'none';
    selectedAddressIdForConfirmation = null;

    // Hapus highlight dari semua item alamat di pop-up
    document.querySelectorAll('.address-item').forEach(item => {
        item.classList.remove('selected');
        // KEMBALIKAN KE BORDER 3px TRANSPARAN
        item.style.border = '3px solid transparent';
    });
    console.log("DEBUG: Pilihan alamat direset.");
}

// Fungsi untuk memperbarui tampilan alamat pengiriman di halaman utama
function updateCurrentShippingAddress(addresses) {
    // Memastikan elemen DOM target ditemukan
    if (!currentShippingAddressDiv) {
        console.error("ERROR: Elemen #shippingAddressDisplayContainer tidak ditemukan untuk memperbarui tampilan alamat.");
        return;
    }
    console.log("DEBUG: updateCurrentShippingAddress dipanggil dengan alamat:", addresses);

    // Mencari alamat utama (primary)
    const primaryAddress = addresses.find(addr => addr.is_primary);
    let addressContentHtml = '';

    // PERBAIKAN UTAMA DI SINI: Mengubah format tampilan alamat
    if (primaryAddress) {
        addressContentHtml = `
            <strong style="font-size: 1.1em;">${primaryAddress.label || primaryAddress.receiver_name || 'Alamat Utama'}</strong><br>
            <span>${primaryAddress.receiver_name} - ${primaryAddress.phone_number}</span><br>
            ${primaryAddress.address}, ${primaryAddress.kelurahan_desa}, ${primaryAddress.kecamatan}, ${primaryAddress.kota_kabupaten}, ${primaryAddress.provinsi}, ${primaryAddress.kode_pos}
        `;
    }
    // Jika tidak ada alamat utama tapi ada alamat lain, tampilkan alamat pertama dan pesan untuk memilih utama
    else if (addresses.length > 0) {
        const firstAddress = addresses[0];
        addressContentHtml = `
            <span style="color: orange; font-size: 0.9em;">(Pilih alamat utama dari daftar)</span><br>
            <strong style="font-size: 1.1em;">${firstAddress.label || firstAddress.receiver_name || 'Alamat'}</strong><br>
            <span>${firstAddress.receiver_name} - ${firstAddress.phone_number}</span><br>
            ${firstAddress.address}, ${firstAddress.kelurahan_desa}, ${firstAddress.kecamatan}, ${firstAddress.kota_kabupaten}, ${firstAddress.provinsi}, ${firstAddress.kode_pos}
        `;
    }
    // Jika tidak ada alamat sama sekali
    else {
        addressContentHtml = `
            <span style="font-weight:bold;">Alamat pengiriman belum diatur. Silakan tambahkan!</span>
        `;
    }

    // Memperbarui innerHTML dari kontainer alamat pengiriman
    currentShippingAddressDiv.innerHTML = `
        <i class="bi bi-geo-alt me-2" style="font-size: 20px; color: #52282A;"></i>
        <p class="mb-0" style="color: #52282A; margin-top: 0; line-height: 1.2;">
            ${addressContentHtml}
        </p>
    `;
}

// Fungsi untuk mengambil dan menampilkan alamat di pop-up
async function fetchAndRenderAddresses() {
    // Memastikan semua elemen DOM dan variabel penting sudah siap
    if (!addressList || typeof CURRENT_USER_ID === 'undefined' || !csrfToken || !confirmAddressBtn) {
        console.error("DEBUG: Elemen DOM penting (addressList, confirmAddressBtn) atau CURRENT_USER_ID/csrfToken belum siap saat fetchAndRenderAddresses dipanggil.");
        addressList.innerHTML = '<p style="color: red;">Error in setup, cannot load addresses.</p>';
        return;
    }
    // Memastikan CURRENT_USER_ID bukan null (pengguna harus login)
    if (CURRENT_USER_ID === null) {
        addressList.innerHTML = '<p style="color: #F8F0E3;">Silakan login untuk mengelola alamat Anda.</p>';
        updateCurrentShippingAddress([]);
        if (confirmAddressBtn) confirmAddressBtn.style.display = 'none';
        console.log("DEBUG: Pengguna tidak login, tidak dapat memuat alamat.");
        return;
    }

    console.log("DEBUG: fetchAndRenderAddresses dipanggil.");
    addressList.innerHTML = '<p style="color: #F8F0E3;">Memuat alamat...</p>'; // Tampilkan pesan loading
    confirmAddressBtn.style.display = 'none'; // Sembunyikan tombol konfirmasi
    selectedAddressIdForConfirmation = null; // Reset pilihan alamat

    // Reset border semua item alamat
    document.querySelectorAll('.address-item').forEach(item => {
        item.classList.remove('selected');
        item.style.border = '3px solid transparent';
    });


    try {
        // PERBAIKAN UTAMA DI SINI: Memanggil API dengan CURRENT_USER_ID
        const response = await fetch(`/api/users/${CURRENT_USER_ID}/addresses`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Mengirim CSRF token untuk keamanan
            }
        });

        // Penanganan jika respons tidak OK (misal: 404, 500, 403)
        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response Text:', response.status, errorText);
            throw new Error(`Server respons dengan status ${response.status}: ${errorText.substring(0, 100)}...`);
        }

        const data = await response.json(); // Parse respons JSON
        console.log("DEBUG: API Success Data:", data);

        // Jika API berhasil dan ada alamat yang diterima
        if (data.success && data.addresses.length > 0) {
            addressList.innerHTML = ''; // Kosongkan daftar alamat sebelumnya
            let initialSelectedAddressFound = false; // Flag untuk menandai alamat utama pertama

            // Iterasi setiap alamat dan buat elemen DOM untuk ditampilkan
            data.addresses.forEach(address => {
                const addressItem = document.createElement('div');
                addressItem.className = 'address-item';
                addressItem.dataset.addressId = address.id; // Simpan ID alamat di dataset

                // Styling inline untuk item alamat
                addressItem.style.cssText = `
                    background-color: #FFF8E2;
                    border-radius: 10px;
                    padding: 15px;
                    margin-bottom: 10px;
                    display: flex;
                    align-items: flex-start;
                    cursor: pointer;
                    border: 3px solid transparent; /* DEFAULT BORDER 3px TRANSPARAN */
                    transition: border-color 0.2s ease-in-out; /* Animasi untuk transisi border */
                `;

                // Menandai dan memberi border pada alamat utama/terpilih secara default
                if (address.is_primary && !initialSelectedAddressFound) {
                    addressItem.classList.add('selected');
                    addressItem.style.border = '3px solid #52282A'; // Border solid untuk yang primary
                    selectedAddressIdForConfirmation = address.id;
                    initialSelectedAddressFound = true;
                    console.log("DEBUG: Alamat primary ditemukan dan dipilih: ", address.id);
                }

                // Konten HTML untuk setiap item alamat
                // PERUBAHAN UTAMA DI SINI: Mengubah format tampilan alamat di pop-up
                addressItem.innerHTML = `
                    <i class="bi bi-geo-alt-fill me-2" style="font-size: 20px; color: #52282A; margin-right: 10px; min-width: 20px;"></i>
                    <span style="color: #52282A; flex-grow: 1; display: block;">
                        <strong style="font-size: 1.1em;">${address.label || address.receiver_name || 'Alamat'}</strong><br>
                        <span>${address.receiver_name} - ${address.phone_number}</span><br>
                        ${address.address}, ${address.kelurahan_desa}, ${address.kecamatan}, ${address.kota_kabupaten}, ${address.provinsi}, ${address.kode_pos}
                    </span>
                `;

                // Event Listener: Ketika item alamat diklik
                addressItem.addEventListener('click', function () {
                    console.log("DEBUG: addressItem diklik:", this.dataset.addressId);
                    // Hapus kelas 'selected' dan reset border dari semua item lain
                    document.querySelectorAll('.address-item').forEach(item => {
                        item.classList.remove('selected');
                        item.style.border = '3px solid transparent';
                    });
                    // Tambahkan kelas 'selected' dan border ke item yang diklik
                    this.classList.add('selected');
                    this.style.border = '3px solid #52282A';

                    // Simpan ID alamat yang dipilih untuk nanti dikonfirmasi
                    selectedAddressIdForConfirmation = parseInt(this.dataset.addressId);
                    console.log("DEBUG: Alamat dipilih sementara:", selectedAddressIdForConfirmation);

                    // Tampilkan tombol konfirmasi
                    if (confirmAddressBtn) {
                        confirmAddressBtn.style.display = 'block';
                        console.log("DEBUG: confirmAddressBtn diharapkan muncul setelah klik item.");
                    }
                });
                addressList.appendChild(addressItem); // Tambahkan item ke daftar alamat
            });

            // Pastikan tombol "Pilih Alamat" muncul jika ada alamat yang sudah terpilih secara default setelah render
            if (selectedAddressIdForConfirmation !== null) {
                if (confirmAddressBtn) confirmAddressBtn.style.display = 'block';
                console.log("DEBUG: Tombol Pilih Alamat ditampilkan karena ada pilihan awal (setelah render).");
            } else {
                if (confirmAddressBtn) confirmAddressBtn.style.display = 'none';
                console.log("DEBUG: Tidak ada pilihan awal, tombol Pilih Alamat disembunyikan.");
            }

            // Perbarui tampilan alamat pengiriman di halaman utama
            updateCurrentShippingAddress(data.addresses);
        }
        // Jika API berhasil tapi tidak ada alamat yang diterima
        else {
            // PERBAIKAN WARNA TEKS: Mengubah warna teks menjadi #52282A agar lebih terlihat
            addressList.innerHTML = '<p style="color: #52282A;">Anda belum memiliki alamat. Silakan tambahkan alamat baru.</p>';
            updateCurrentShippingAddress([]); // Perbarui tampilan alamat utama menjadi kosong
            if (confirmAddressBtn) confirmAddressBtn.style.display = 'none';
            console.log("DEBUG: Tidak ada alamat, tombol Pilih Alamat disembunyikan.");
        }
    } catch (error) {
        // Penanganan error saat fetching alamat
        console.error('Error fetching addresses:', error);
        addressList.innerHTML = `<p style="color: red;">Gagal memuat alamat: ${error.message}</p>`;
        if (confirmAddressBtn) confirmAddressBtn.style.display = 'none';
        console.log("DEBUG: Error memuat alamat, tombol Pilih Alamat disembunyikan.");
    }
}
// --- AKHIR FUNGSI PEMBANTU ---


// --- KODE UTAMA YANG MENJALANKAN SAAT DOM SIAP ---
document.addEventListener('DOMContentLoaded', function () {
    console.log("DEBUG: 1. DOM Content Loaded - address.js script is running.");

    // Mendapatkan elemen DOM dan variabel global yang akan digunakan
    addressModal = document.getElementById('addressModal');
    addAddressFormModal = document.getElementById('addAddressFormModal');
    addressList = document.getElementById('addressList');
    tambahAlamatBtn = document.getElementById('tambahAlamatBtn');
    newAddressForm = document.getElementById('newAddressForm');
    // Mengambil CSRF token dari meta tag
    csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    confirmAddressBtn = document.getElementById('confirmAddressBtn');


    // DEBUG: Pastikan semua elemen DOM penting ditemukan
    console.log("DEBUG: Elements loaded:", {
        addressModal: !!addressModal,
        addAddressFormModal: !!addAddressFormModal,
        addressList: !!addressList,
        tambahAlamatBtn: !!tambahAlamatBtn,
        newAddressForm: !!newAddressForm,
        csrfToken: !!csrfToken,
        confirmAddressBtn: !!confirmAddressBtn
    });

    // Mengalihkan currentShippingAddressDiv ke kontainer utama alamat untuk update tampilan
    const shippingAddressDisplayContainer = document.getElementById('shippingAddressDisplayContainer');
    if (shippingAddressDisplayContainer) {
        currentShippingAddressDiv = shippingAddressDisplayContainer;
        console.log("DEBUG: currentShippingAddressDiv berhasil dialihkan ke #shippingAddressDisplayContainer.");
    } else {
        console.error("ERROR: Elemen #shippingAddressDisplayContainer tidak ditemukan di DOM!");
        // Fallback jika kontainer utama tidak ditemukan
        currentShippingAddressDiv = document.getElementById('currentShippingAddress');
    }


    // Pastikan CURRENT_USER_ID sudah didefinisikan dari Blade (dari layouts/app.blade.php atau cart.blade.php)
    if (typeof CURRENT_USER_ID === 'undefined' || CURRENT_USER_ID === null) {
        console.error("DEBUG: 2. ERROR: CURRENT_USER_ID tidak didefinisikan atau null. Fungsionalitas alamat tidak akan bekerja penuh.");
        // Jika tidak ada user ID, mungkin ingin menonaktifkan beberapa fungsi
        // atau menampilkan pesan yang sesuai kepada pengguna.
        // Tidak perlu 'return' di sini jika ingin tetap menampilkan modal tambah alamat
    } else {
        console.log("DEBUG: 2. CURRENT_USER_ID ditemukan:", CURRENT_USER_ID);
    }

    // Event Listener untuk tombol "Ubah Alamat" di halaman utama
    const ubahAlamatBtn = document.getElementById('ubahAlamatBtn');
    if (ubahAlamatBtn) {
        console.log("DEBUG: 3. Tombol 'ubahAlamatBtn' ditemukan di DOM.");
        ubahAlamatBtn.addEventListener('click', function () {
            console.log("DEBUG: 4. Tombol 'ubahAlamatBtn' DIKLIK!");
            openModal(addressModal); // Buka modal daftar alamat
            fetchAndRenderAddresses(); // Ambil dan tampilkan alamat
        });
    } else {
        console.error("3. ERROR: Tombol 'ubahAlamatBtn' TIDAK DITEMUKAN di DOM. Periksa ID di HTML.");
    }

    // Event Listener untuk tombol "Tutup" modal (ikon X di header)
    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', function () {
            closeModal(addressModal);
            closeModal(addAddressFormModal);
        });
    });
    // Event Listener untuk tombol "Batal" di form tambah alamat
    document.querySelectorAll('.close-add-form-button').forEach(button => {
        button.addEventListener('click', function () {
            closeModal(addAddressFormModal);
            openModal(addressModal); // Kembali ke modal daftar alamat
        });
    });

    // --- LOGIC: Event Listener untuk tombol "Pilih Alamat" ---
    if (confirmAddressBtn) {
        confirmAddressBtn.addEventListener('click', async function () {
            console.log("DEBUG: Tombol 'confirmAddressBtn' diklik.");
            if (selectedAddressIdForConfirmation !== null) {
                try {
                    // Panggilan API untuk mengatur alamat utama
                    const response = await fetch(`/api/users/${CURRENT_USER_ID}/addresses/${selectedAddressIdForConfirmation}/set-primary`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('API Error Response Text (Confirm Set Primary):', errorText);
                        throw new Error(`Gagal mengatur alamat utama: ${response.status}: ${errorText.substring(0, 100)}...`);
                    }

                    const data = await response.json();
                    if (data.success) {
                        alert(data.message); // Gunakan modal kustom di produksi, bukan alert
                        closeModal(addressModal); // Tutup modal
                        fetchAndRenderAddresses(); // Muat ulang alamat untuk update tampilan
                    }
                } catch (error) {
                    console.error('Error confirming primary address:', error);
                    alert('Terjadi kesalahan saat mengatur alamat utama: ' + error.message);
                }
            } else {
                alert('Pilih salah satu alamat terlebih dahulu!');
            }
        });
    } else {
        console.error("DEBUG: Tombol 'confirmAddressBtn' tidak ditemukan (di DOMContentLoaded).");
    }

    // Event Listener untuk tombol "Tambah Alamat" di modal daftar
    if (tambahAlamatBtn) {
        tambahAlamatBtn.addEventListener('click', function () {
            console.log("DEBUG: Tombol 'Tambah Alamat' diklik.");
            closeModal(addressModal); // Tutup modal daftar alamat
            newAddressForm.reset(); // Reset form tambah alamat
            openModal(addAddressFormModal); // Buka modal form tambah alamat
        });
    } else {
        console.error("DEBUG: Tombol 'tambahAlamatBtn' tidak ditemukan (di DOMContentLoaded).");
    }

    // Event Listener untuk submit form "Tambah Alamat Baru"
    newAddressForm.addEventListener('submit', async function (event) {
        event.preventDefault(); // Mencegah submit form default
        console.log("DEBUG: Form 'Tambah Alamat Baru' disubmit.");

        const formData = new FormData(this); // Mengambil data dari form
        const data = Object.fromEntries(formData.entries()); // Mengubah FormData menjadi objek JSON
        data.is_primary = data.is_primary ? true : false; // Konversi checkbox menjadi boolean

        try {
            // Panggilan API untuk menyimpan alamat baru
            const response = await fetch(`/api/users/${CURRENT_USER_ID}/addresses`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data) // Mengirim data sebagai JSON
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response Text (Save Address):', errorText);
                let errorMessage = 'Gagal menyimpan alamat.';
                // Mencoba parse error JSON untuk detail validasi
                if (response.headers.get('content-type')?.includes('application/json')) {
                    const errorJson = JSON.parse(errorText);
                    if (errorJson.errors) {
                        errorMessage += '\n\nDetail Validasi:\n' + Object.values(errorJson.errors).flat().join('\n');
                    } else if (errorJson.message) {
                        errorMessage = errorJson.message;
                    }
                } else {
                    errorMessage += '\n\nRespons server: ' + errorText.substring(0, 100) + '...';
                }
                throw new Error(errorMessage);
            }

            const result = await response.json();
            if (result.success) {
                alert(result.message); // Gunakan modal kustom di produksi, bukan alert
                closeModal(addAddressFormModal); // Tutup modal form
                openModal(addressModal); // Kembali ke modal daftar alamat
                fetchAndRenderAddresses(); // Muat ulang alamat untuk update tampilan
            }
        } catch (error) {
            console.error('Error saving new address:', error);
            alert('Terjadi kesalahan saat menyimpan alamat: ' + error.message);
        }
    });

    // Panggil ini saat halaman pertama kali dimuat untuk menampilkan alamat utama
    // Ini akan memicu pengambilan alamat saat halaman keranjang dimuat
    fetchAndRenderAddresses();
});
