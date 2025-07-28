<x-app2>
    {{-- ntar ini apus aja bang, fungsinya, kalo gaada cart active,
    bakal muncul error message, harusnya nanti di bagian cart, udah ada validasinya
    jadi ini gk perlu --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    <div class="container py-4">
        <div class="mb-3">
            <a href="#" class="text-decoration-none text-dark">
                <i class="fa-solid fa-arrow-left fa-2xl" style="color: #52282A;"></i>
            </a>
        </div>

        <div class="modal-content rounded-4 border-0" style="background-color: #fffaf0;">
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-clock-fill text-warning me-2"></i>Bayar sebelum
                        </h5>
                        <small class="text-muted" id="paymentDeadline"></small>
                    </div>
                    <div class="text-center d-flex" id="countdownContainer">
                        <div class="bg-danger text-white rounded-pill px-2 py-1 small fw-bold" id="countdownHour">--
                            <small>Jam</small></div>
                        <div class="bg-danger text-white rounded-pill px-2 py-1 small fw-bold mt-1"
                            id="countdownMinute">-- <small>Menit</small></div>
                        <div class="bg-danger text-white rounded-pill px-2 py-1 small fw-bold mt-1"
                            id="countdownSecond">-- <small>Detik</small></div>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="mb-1 text-muted">Nomor Virtual Account</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">1234135139348783</h5>
                        @if ($order->payment_method == "BCA")
                        <img id="modalSelectedBankLogo" src="{{asset('assets/bank_logo/BCA.png')}}" alt="Logo Bank" style="height: 28px;">
                        @elseif ($order->payment_method == "Mandiri")
                        <img id="modalSelectedBankLogo" src="{{asset('assets/bank_logo/Mandiri.png')}}" alt="Logo Bank" style="height: 28px;">
                        @elseif ($order->payment_method == "Cimb Niaga")
                        <img id="modalSelectedBankLogo" src="{{asset('assets/bank_logo/CimbNiaga.png')}}" alt="Logo Bank" style="height: 28px;">
                        @else
                        <img id="modalSelectedBankLogo" src="{{asset('assets/bank_logo/Danamon.png')}}" alt="Logo Bank" style="height: 28px;">
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <p class="mb-1 text-muted">Total Tagihan</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">Rp109.500</h5>
                    </div>
                </div>

                <form action="" method="post">
                    @csrf
                    <a href="{{route('orders.index')}}" id="confirmPaymentBtn" class="btn btn-bayar w-100 mt-3">Lihat Pesanan saya</a>
                </form>
            </div>
        </div>
        {{-- <!-- Stepper -->
        <div class="py-1">
            <div class="stepper">
                <div class="step completed">
                    <div class="circle"></div>
                    <div class="step-label">Cart</div>
                </div>
                <div class="step active">
                    <div class="circle"></div>
                    <div class="step-label">Checkout</div>
                </div>
                <div class="step">
                    <div class="circle"></div>
                    <div class="step-label">Bayar</div>
                </div>
            </div>
        </div>

        <h4 class="fw-bold text-brown">Checkout</h4>
        <hr>

        <div class="mb-3 text-center">
            <i class="bi bi-credit-card me-2"></i> <strong>Metode Pembayaran</strong>
        </div>

        <div class="row">
            <!-- Left: Payment methods -->
            <div class="col-md-8 mb-3">
                <div class="payment-box">
                    <h6 class="fw-bold text-secondary">Bank Transfer – Virtual Account</h6>
                    <p class="mb-2">Transfer pembayaran ke nomor virtual account melalui m-banking, internet banking,
                        atau ATM. Pembayaran akan terkonfirmasi secara otomatis.</p>
                    <p class="fw-medium">Pilih salah satu:</p>

                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $banks = [
                                ['name' => 'BCA', 'image' => 'BCA.png'],
                                ['name' => 'CimbNiaga', 'image' => 'CimbNiaga.png'],
                                ['name' => 'Danamon', 'image' => 'Danamon.png'],
                                ['name' => 'Mandiri', 'image' => 'Mandiri.png'],
                            ];
                        @endphp

                        @foreach ($banks as $bank)
                            <div class="bank-option border p-2 rounded" data-bank="{{ $bank['name'] }}"
                                data-logo="{{ asset('assets/bank_logo/' . $bank['image']) }}">
                                <img src="{{ asset('assets/bank_logo/' . $bank['image']) }}" alt="{{ $bank['name'] }}"
                                    style="height: 30px; width: auto;" class="img-fluid">
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        <strong>Bank yang dipilih:</strong><br>
                        <img id="selectedBankLogo" src="" alt="Bank Logo" style="height: 40px; display: none;">
                    </div>
                </div>
            </div>

            <!-- Right: Summary -->
            <div class="col-md-4">
                <div class="summary-box">
                    <h6 class="fw-bold">Pilih Metode Pembayaran</h6>
                    <hr>
                    <p class="text-muted small">Tidak ada biaya sampai anda meninjau dan melakukan pesanan.</p>

                    <form id="checkoutForm" action="{{ route('checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" id="payment_method">
                        <button type="button" id="openPaymentModal" class="btn btn-bayar w-100 mt-3">Bayar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0" style="background-color: #fffaf0;">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1"><i class="bi bi-clock-fill text-warning me-2"></i>Bayar sebelum
                                </h5>
                                <small class="text-muted" id="paymentDeadline"></small>
                            </div>
                            <div class="text-center" id="countdownContainer">
                                <div class="bg-danger text-white rounded-pill px-2 py-1 small fw-bold"
                                    id="countdownHour">-- <small>Jam</small></div>
                                <div class="bg-danger text-white rounded-pill px-2 py-1 small fw-bold mt-1"
                                    id="countdownMinute">-- <small>Menit</small></div>
                                <div class="bg-danger text-white rounded-pill px-2 py-1 small fw-bold mt-1"
                                    id="countdownSecond">-- <small>Detik</small></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1 text-muted">Nomor Virtual Account</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">1234135139348783</h5>
                                <img id="modalSelectedBankLogo" src="" alt="Logo Bank" style="height: 28px;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="mb-1 text-muted">Total Tagihan</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold text-dark mb-0">Rp109.500</h5>
                            </div>
                        </div>

                        <form action="" method="post">
                            @csrf
                            <button type="submit" id="confirmPaymentBtn" class="btn btn-bayar w-100 mt-3">Bayar
                                Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> --}}

        <script>
            let deadline = null;
            let timerInterval;

            window.addEventListener('DOMContentLoaded', () => {
                const selectedBankLogo = document.getElementById('selectedBankLogo');
                const modalSelectedBankLogo = document.getElementById('modalSelectedBankLogo');
                const openPaymentModalBtn = document.getElementById('openPaymentModal');
                const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
                const paymentDeadlineText = document.getElementById('paymentDeadline');
                const checkoutForm = document.getElementById('checkoutForm');
                const paymentMethodInput = document.getElementById('payment_method');

                document.querySelectorAll('.bank-option').forEach(option => {
                    option.addEventListener('click', function() {
                        document.querySelectorAll('.bank-option').forEach(el => el.classList.remove(
                            'selected'));
                        this.classList.add('selected');

                        const logo = this.getAttribute('data-logo');
                        const bankCode = this.getAttribute('data-bank');

                        if (logo) {
                            selectedBankLogo.src = logo;
                            selectedBankLogo.style.display = 'inline';
                            modalSelectedBankLogo.src = logo;
                        }

                        if (paymentMethodInput) {
                            paymentMethodInput.value = bankCode;
                        }
                    });
                });

                openPaymentModalBtn?.addEventListener('click', function(e) {
                    e.preventDefault();

                    const selectedBank = document.querySelector('.bank-option.selected');
                    if (!selectedBank) {
                        alert("Silakan pilih metode pembayaran terlebih dahulu.");
                        return;
                    }

                    deadline = new Date().getTime() + 24 * 60 * 60 * 1000;
                    const deadlineDate = new Date(deadline);
                    const formatted = deadlineDate.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }).replace(/\./g, ':');

                    paymentDeadlineText.innerText = `${formatted} WIB`;
                    const modalEl = document.getElementById('paymentModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    clearInterval(timerInterval);
                    updateCountdown();
                    timerInterval = setInterval(updateCountdown, 1000);
                });

                // confirmPaymentBtn?.addEventListener('click', function() {
                //     clearInterval(timerInterval);
                //     checkoutForm?.submit();
                // });
            });

            function updateCountdown() {
                if (!deadline) return;

                const now = new Date().getTime();
                const distance = deadline - now;

                const countdownHour = document.getElementById("countdownHour");
                const countdownMinute = document.getElementById("countdownMinute");
                const countdownSecond = document.getElementById("countdownSecond");

                if (distance <= 0) {
                    countdownHour.innerHTML = "00 <small>Jam</small>";
                    countdownMinute.innerHTML = "00 <small>Menit</small>";
                    countdownSecond.innerHTML = "00 <small>Detik</small>";
                    clearInterval(timerInterval);
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownHour.innerHTML = `${String(hours).padStart(2, '0')} <small>Jam</small>`;
                countdownMinute.innerHTML = `${String(minutes).padStart(2, '0')} <small>Menit</small>`;
                countdownSecond.innerHTML = `${String(seconds).padStart(2, '0')} <small>Detik</small>`;
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</x-app2>
