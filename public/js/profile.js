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
