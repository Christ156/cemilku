@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/order_style.css') }}">
@endsection

@section('content')
    <div class="container py-4" style="background-color: #FFFBEF;">
        <!-- Tabs + Search -->
        @php
            $statuses = [
                'all' => 'Semua',
                'pending' => 'Belum Bayar',
                'paid' => 'Diproses',
                'shipped' => 'Dikirim',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
            $activeStatus = request('status', 'all');
        @endphp

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            {{-- Tabs --}}
            <div class="d-flex flex-wrap gap-2">
                @foreach ($statuses as $key => $label)
                    <a href="{{ route('orders.index', ['status' => $key] + request()->except('page')) }}"
                        class="btn tab-btn {{ $activeStatus === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form class="d-flex mt-2 mt-lg-0" role="search" method="GET" action="{{ route('orders.index') }}">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                <input class="form-control border-warning me-2" type="search" name="search" placeholder="Cari pesanan"
                    value="{{ request('search') }}" aria-label="Search" style="background-color: #fffbe0;">
                <button class="btn btn-outline-warning" type="submit">Cari</button>
            </form>
        </div>


        <hr class="text-warning">

        <!-- Card Order -->
        {{-- <div class="card shadow-sm border border-warning rounded-4 p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-medium">INV/20250520/STW0001</small>
                    <button class="btn btn-sm fw-bold text-white" style="background-color: #00D9F5;">Dikirim</button>
                </div>

                <hr class="text-warning">

                <div class="d-flex align-items-center mb-2">
                    <div class="me-3"
                        style="width: 64px; height: 64px; background-color: #f5f5f5; border-radius: 4px;">
                    </div>
                    <div>
                        <div class="fw-bold">Custom Snack Tower – 4 Layer</div>
                        <div class="text-muted small">1 barang x Rp200.000</div>
                    </div>
                    <div class="ms-auto text-end">
                        <div class="fw-bold">Total</div>
                        <div>Rp200.000</div>
                    </div>
                </div>

                <hr class="text-warning">

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">20 Mei 2025 23:59</small>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-warning btn-sm me-2">Lihat detail transaksi</button>
                        <input type="checkbox" class="form-check-input">
                    </div>
                </div>
            </div> --}}

        @if ($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <h5>Belum ada pesanan</h5>
            </div>
        @endif

        @foreach ($orders as $order)
            @if ($orders->isEmpty())
                <div class="d-flex justify-content-center align-items-center">
                    <p class="text-center fw-bold text-muted my-0">Belum ada pesanan</p>
                </div>
             @endif

            <div class="card custombg shadow-sm border border-warning rounded-4 p-3 mb-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-medium">
                        INV/{{ $order->created_at->format('Ymd') }}/{{ sprintf('%05d', $order->id) }}
                    </small>

                    <button class="btn btn-sm fw-bold text-white"
                        style="background-color: {{ \App\Http\Controllers\OrderController::getStatusColor($order->status) }}">
                        {{ $statuses[$order->status] ?? ucfirst($order->status) }}
                    </button>
                </div>

                <hr class="text-warning">

                <!-- Item list -->
                @foreach ($order->orderDetails as $item)
                    @php
                        $product = $item->collection ?? $item->customize;
                        $name = $product->name ?? 'Item Tidak Diketahui';
                        $image = $product->image ?? null;
                        $price = $item->price ?? 0;
                        $quantity = $item->quantity ?? 1;
                    @endphp
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3"
                            style="width: 64px; height: 64px; background-color: #f5f5f5; border-radius: 4px; overflow: hidden;">
                            @if ($image)
                                <img src="{{ asset('assets/collections/' . $image) }}" alt="Item Image"
                                    class="img-fluid rounded" style="width: 64px; height: 64px; object-fit: cover;">
                            @endif
                        </div>
                        <div>
                            <div class="fw-bold">{{ $name }}</div>
                            <div class="text-muted small">{{ $quantity }} barang x
                                Rp{{ number_format($price, 0, ',', '.') }}</div>
                        </div>
                        <div class="ms-auto text-end">
                            <div class="fw-bold">Total</div>
                            <div>Rp{{ number_format($price * $quantity, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach

                <hr class="text-warning">

                <!-- Footer -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-brown btn-sm me-2" data-bs-toggle="modal"
                            data-bs-target="#orderModal{{ $order->id }}">
                            Lihat detail transaksi
                        </button>
                    </div>
                </div>
            </div>


            <!-- Modal Detail Order -->
            <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1"
                aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content rounded-4 custombg2">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold" id="orderModalLabel{{ $order->id }}">Detail
                                Transaksi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Status & Tanggal -->
                            <div class="mb-4">
                                <h5 class="fw-bold customtext">Pesanan
                                    {{ $statuses[$order->status] ?? ucfirst($order->status) }}</h5>
                                <small class="d-block">No. Pesanan: <span class="customtext fw-semibold">
                                        INV/{{ $order->created_at->format('Ymd') }}/{{ sprintf('%05d', $order->id) }}
                                    </span>
                                </small>

                                <small>Tanggal Pembelian: {{ $order->created_at->format('d M Y, H:i') }}</small>
                            </div>

                            <hr>

                            <!-- Detail Produk -->
                            <div class="mb-4">
                                <h6 class="fw-bold">Detail Produk</h6>
                                @foreach ($order->orderDetails as $item)
                                    @php
                                        $product = $item->collection ?? $item->customize;
                                        $name = $product->name ?? 'Item Tidak Diketahui';
                                        $image = $product->image ?? null;
                                        $price = $item->price ?? 0;
                                        $quantity = $item->quantity ?? 1;
                                    @endphp
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-3">
                                            <img src="{{ $image ? asset('assets/collections/' . $image) : 'https://via.placeholder.com/64' }}"
                                                width="64" height="64" class="rounded">
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $name }}</div>
                                            <div class="text-muted small">{{ $quantity }} x
                                                Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr>

                            <!-- Info Pengiriman -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold">Info Pengiriman</h6>
                                </div>
                                <div class="small">
                                    <div class="mt-2">Alamat:</div>
                                    <br>
                                    <div class="fw-semibold">{{ $order->address->receiver_name }}</div>
                                    <div>{{ $order->address->phone_number }}</div>
                                    <div>
                                        {{ $order->address->address }}<br>
                                        {{ $order->address->kelurahan_desa }},
                                        {{ $order->address->kecamatan }}<br>
                                        {{ $order->address->kota_kabupaten }},
                                        {{ $order->address->provinsi }}
                                        {{ $order->address->kode_pos }}
                                    </div>
                                </div>
                            </div>


                            <hr>

                            <!-- Rincian Pembayaran -->
                            <div class="mb-4">
                                <h6 class="fw-bold">Rincian Pembayaran</h6>
                                <div class="small">Metode Pembayaran:
                                    <strong>{{ $order->payment_method }}</strong>
                                </div>
                                <div class="fw-bold mt-2 text-end">Total:
                                    Rp{{ number_format($order->total_price, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>

                            @if ($order->status === 'pending')
                                <form action="{{ route('orders.pay', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">Bayar Sekarang</button>
                                </form>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @endforeach


    </div>

    {{-- JS BANG --}}
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
@endsection
{{-- <x-app2>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Checkout</title>
        <link rel="stylesheet" href="{{ asset('css/order_style.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    </head>

    <body>


    </body>


</x-app2> --}}
