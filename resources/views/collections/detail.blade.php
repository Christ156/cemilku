@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/collection_detail.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/collection_detail.js') }}"></script>
@endsection

@section('content')

    {{-- Alert --}}
    <div id="topAlertContainer">
        <span id="topAlertMessage"></span>
</div>

    <div id="alertBox" class="alert-text mt-2" role="alert" style="margin-top: 100px;">
        <span id="alertMessage">Oops! Maximum stock limit reached.</span>
    </div>

    {{-- BACK BUTTON --}}
    <div class="back-button d-flex w-100 justify-content-between align-items-center" style="margin-top: 80px">
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
                <div class="size-label">SIZE</div>
                <div class="size-value">85,6 cm (H) x 25 cm (W)</div>

                {{-- FORM ADD TO CART --}}
                <form action="{{ route('collections.store', $detail->id) }}" method="POST">
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
                        <div style="font-weight:600; margin-bottom:0px; font-size: 1.1rem;">QUANTITY</div>
                        <div class="counter-box">
                            <button type="button" id="minus">-</button>
                            <input type="number" id="value" class="counter-value" name="quantity" value="1"
                                min="1" />
                            <button type="button" id="plus">+</button>
                        </div>
                    </div>

                    {{-- BUTTON ADD TO CART AND BUY NOW --}}
                    <div class="button-container d-flex">
                        <button type="button" class="btn btn-warning d-flex align-items-center justify-content-center"
                            style="color: #52282A; border: 1px solid #000000;" id="add-to-cart-detail-btn">
                            <i class="bi bi-cart"></i>
                            <div class="ms-2" style="font-size: 16px">Add To Cart</div>
                        </button>
                        <a href="#" class="btn btn-warning d-flex align-items-center justify-content-center"
                            style="color: #52282A; border: 1px solid #000000;">
                            <div style="font-size: 16px">Buy Now</div>
                        </a>
                    </div>
                </form>

                {{-- TOAST ALERT --}}
                <div id="toastAlert" class="toast-alert">
                    <span id="toastMessage">Oops! Maximum stock limit reached.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Pop Up Success --}}
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
                <h4 class="fw-bold mb-2">Success</h4>
                <p class="mb-4">Collections has been added to cart!</p>

                <div class="d-flex justify-content-center mb-3">
                    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-dismiss="modal">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Trigger Pop Up if Success Add To Cart --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var doneModal = new bootstrap.Modal(document.getElementById('doneModal'));
                doneModal.show();
            });
        </script>
    @endif
@endsection
