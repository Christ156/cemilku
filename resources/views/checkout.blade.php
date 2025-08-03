@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/checkout_style.css') }}">
    <style>
        .countdown-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 60px;
            font-size: 16px;
            line-height: 1.2;
        }

        #countdownContainer {
            gap: 0.5rem;
        }

        .countdown-value {
            font-weight: bold;
            font-size: 18px;
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Error handling for order not found --}}
    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ $errors->first('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container py-4">
        {{-- Check if $order object exists before trying to access its properties --}}
        @if ($order)
            <div class="modal-content rounded-4 border-0" style="background-color: #fffaf0;">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="bi bi-clock-fill text-warning me-2"></i>Bayar sebelum</h5>
                            <small class="text-muted" id="paymentDeadline"></small>
                        </div>
                        <div class="text-center d-flex" id="countdownContainer">
                            <div class="countdown-item bg-danger text-white rounded-pill px-3 py-2 text-center">
                                <div class="countdown-value" id="countdownHour">--</div>
                                <small>Jam</small>
                            </div>
                            <div class="countdown-item bg-danger text-white rounded-pill px-3 py-2 text-center">
                                <div class="countdown-value" id="countdownMinute">--</div>
                                <small>Menit</small>
                            </div>
                            <div class="countdown-item bg-danger text-white rounded-pill px-3 py-2 text-center">
                                <div class="countdown-value" id="countdownSecond">--</div>
                                <small>Detik</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 text-muted">Nomor Virtual Account</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">1234135139348783</h5> {{-- Ini mungkin perlu dinamis juga nanti --}}
                            @if ($order->payment_method == 'BCA')
                                <img id="modalSelectedBankLogo" src="{{ asset('assets/bank_logo/BCA.png') }}" alt="Logo Bank"
                                    style="height: 28px;">
                            @elseif ($order->payment_method == 'Mandiri')
                                <img id="modalSelectedBankLogo" src="{{ asset('assets/bank_logo/Mandiri.png') }}"
                                    alt="Logo Bank" style="height: 28px;">
                            @elseif ($order->payment_method == 'Cimb Niaga')
                                <img id="modalSelectedBankLogo" src="{{ asset('assets/bank_logo/CimbNiaga.png') }}"
                                    alt="Logo Bank" style="height: 28px;">
                            @else {{-- Asumsi ini untuk Danamon --}}
                                <img id="modalSelectedBankLogo" src="{{ asset('assets/bank_logo/Danamon.png') }}"
                                    alt="Logo Bank" style="height: 28px;">
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="mb-1 text-muted">Total Tagihan</p>
                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Ganti hardcoded ini dengan nilai dari $order->total_price --}}
                            <h5 class="fw-bold text-dark mb-0">Rp{{ number_format($order->total_price, 0, ',', '.') }}</h5>
                        </div>
                    </div>

                    {{-- Form ini tidak perlu action POST, hanya link untuk melihat pesanan --}}
                    <form action="" method="get"> {{-- Ganti method="post" ke method="get" atau hapus saja form tag --}}
                        @csrf {{-- CSRF token tidak diperlukan untuk GET request --}}
                        <a href="{{ route('orders.index') }}" id="confirmPaymentBtn" class="btn btn-bayar w-100 mt-3">Lihat
                            Pesanan saya</a>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                Detail order tidak ditemukan. Silakan kembali ke keranjang untuk melakukan checkout.
                <br>
                <a href="{{ route('cart.index', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}" class="btn btn-primary mt-3">Kembali ke Keranjang</a>
            </div>
        @endif


        <script>
            let deadline = null;
            let timerInterval;

            function updateCountdown() {
                if (!deadline) return;

                const now = new Date().getTime();
                const distance = deadline - now;

                const countdownHour = document.getElementById("countdownHour");
                const countdownMinute = document.getElementById("countdownMinute");
                const countdownSecond = document.getElementById("countdownSecond");

                if (distance <= 0) {
                    countdownHour.innerText = "00";
                    countdownMinute.innerText = "00";
                    countdownSecond.innerText = "00";
                    clearInterval(timerInterval);
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownHour.innerText = String(hours).padStart(2, '0');
                countdownMinute.innerText = String(minutes).padStart(2, '0');
                countdownSecond.innerText = String(seconds).padStart(2, '0');
            }

            window.addEventListener('DOMContentLoaded', () => {
                // Set deadline otomatis 24 jam dari sekarang
                // Jika Anda ingin deadline berdasarkan order creation_at, Anda perlu passing data itu dari controller
                // Untuk saat ini, kita akan tetap pakai 24 jam dari DOMContentLoaded sebagai placeholder.
                // Idealnya: deadline = new Date("{{ $order->created_at->addHours(24)->format('Y/m/d H:i:s') }}").getTime();
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

                const paymentDeadlineText = document.getElementById('paymentDeadline');
                paymentDeadlineText.innerText = `${formatted} WIB`;

                clearInterval(timerInterval);
                updateCountdown();
                timerInterval = setInterval(updateCountdown, 1000);
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
@endsection
