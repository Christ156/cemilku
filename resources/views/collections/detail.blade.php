@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/collection_detail.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/collection_detail.js') }}"></script>
@endsection

@section('content')
    {{-- ALERT --}}
    <div id="topAlertContainer">
        <span id="topAlertMessage"></span>
    </div>

    <div id="alertBox" class="alert-text mt-2" role="alert" style="margin-top: 100px;">
        <span id="alertMessage">{{ __('collection.limit') }}</span>
    </div>

    {{-- BACK BUTTON --}}
    <div class="back-button d-flex w-100 justify-content-between align-items-center" style="margin-top: 30px">
        <a href="/collections" id="backBtn">
            <img src="{{ asset('assets/mystery_box/arrow_back.png') }}" alt="Back" style="height: 24px;" />
        </a>
    </div>

    <div class="container-fluid" style="height: 89%; width: 90%;">
        <div class="row align-items-start" style="color: #52282A;">
            {{-- COLLECTION IMAGE --}}
            <div class="collections_img col text-center justify-content-center">
                <img src="{{ asset('assets/collections/' . $detail->image) }}" alt="{{ $detail->name }}"
                    style="border-radius: 20px; object-fit:contain; height: 550px;">
            </div>

            <div class="vertical-line" style="width: 1px;"></div>

            {{-- RIGHT CONTENT --}}
            <div class="col">
                <div class="subtitle">{{ $detail->category }}</div>
                <div class="title">{{ $detail->name }}</div>
                <div class="price-tag">Rp {{ number_format($detail->price, 0, ',', '.') }}</div>
                <div class="description" style="text-align: justify;">
                    <p class="card-text">{{ $detail->description }} </p>
                </div>
                <div class="size-label">{{ __('collection.size') }}</div>
                <div class="size-value">85,6 cm ({{ __('collection.height') }}) x 25 cm ({{ __('collection.width') }})</div>

                {{-- FORM ADD TO CART --}}
                <form id="addToCartForm">
                    @csrf
                    <input type="hidden" name="collection_id" value="{{ $detail->id }}">
                    <input type="hidden" name="price" value="{{ $detail->price }}">
                    <input type="hidden" id="stock" value="{{ $detail->stock }}">
                    <input type="hidden" id="item-id" value="{{ $detail->id }}">
                    <input type="hidden" id="item-name" value="{{ $detail->name }}">
                    <input type="hidden" id="item-price" value="{{ $detail->price }}">
                    <input type="hidden" id="item-image" value="{{ asset('assets/collections/' . $detail->image) }}">
                    <input type="hidden" id="item-description" value="{{ $detail->description }}">

                    {{-- BUTTON QUANTITY --}}
                    <div class="counter-container">
                        <div class="qty-label" style="font-weight:600; margin-bottom:0px;">QUANTITY</div>
                        <div class="counter-box" onchange="checkQuantityValid({{$detail->stock}})">
                            <button type="button" id="subs_quantity" onclick="setQuantity('subs', {{$detail->stock}})">-</button>
                            <input type="number" id="value_quantity" class="counter-value" name="quantity" value="1" min="1"/>
                            <button type="button" id="add_quantity" onclick="setQuantity('add', {{$detail->stock}})">+</button>
                        </div>
                    </div>

                    <div class="button-container d-flex add-to-cart-fixed-position">
                        <button type="button" class="btn btn-warning d-flex align-items-center justify-content-center"
                            style="color: #52282A; border: 1px solid #000000;" id="add-to-cart-detail-btn">
                            <i class="bi bi-cart"></i>
                            <div class="addcart ms-2">{{ __('collection.addToCart') }}</div>
                        </button>
                    </div>
                </form>

                <div id="toastAlert" class="toast-alert">
                    <span id="toastMessage">{{ __('collection.limit') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="doneModal" tabindex="-1" aria-labelledby="doneModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 p-4 text-center">
                <div class="success-icon mx-auto mb-3 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none"
                        viewBox="0 0 64 64">
                        <rect width="64" height="64" rx="12" fill="#28a745" />
                        <path stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                            d="M18 34l10 10 18-24" />
                    </svg>
                </div>
                <h4 class="fw-bold mb-2">{{ __('collection.success') }}</h4>
                <p class="mb-4">{{ __('collection.collectionAdded') }}</p>

                <div class="d-flex justify-content-center mb-3">
                    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-dismiss="modal">
                        {{ __('collection.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successAddToCartModal" tabindex="-1" aria-labelledby="successAddToCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="successAddToCartModalLabel">Congratulations!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center"> {{-- Hapus justify-content-center dari sini --}}
                    <div>
                        <p class="text-center">
                            Horeyyy, Snack Bouquet kamu sudah berhasil dibuat dan masuk ke keranjang!!!
                        </p>
                    </div>

                    <div class="d-flex justify-content-center w-100">
                        <a href="{{ route('cart.index', ['id_user' => Auth::id(), 'slug' => Str::slug(Auth::user()->name)]) }}" class="btn btn-warning">Lihat keranjang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trigger Pop Up if Success Add To Cart (Dihapus karena diganti dengan JS Ajax, tapi saya kembalikan sesuai permintaan Anda untuk tidak mengubah yang tidak berhubungan. Namun, jika Anda menggunakan AJAX, bagian ini tidak akan terpicu oleh AJAX.) --}}
    {{-- @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var doneModal = new bootstrap.Modal(document.getElementById('doneModal'));
                doneModal.show();
            });
        </script>
    @endif --}}
@endsection
