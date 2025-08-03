@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/mysterybox.css') }}" />
@endsection

@section('script')
    <script src="{{ asset('js/mysterybox.js') }}"></script>
@endsection

@section('content')
    <div class="container-fluid min-vh-100 d-flex flex-column flex-md-row" id="mysteryboxContainer"
        data-mode="{{ $mode }}">

        <div class="container-fluid d-flex flex-column flex-md-row" data-mode="{{ $mode }}">

            {{-- LEFT SIDE --}}
            <div class="left-section col-12 col-md-6 mt-3 d-flex flex-column">
                {{-- Header --}}
                <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                    <a href="#" id="backBtn" data-home-url="{{ route('home') }}">
                        <img src="{{ asset('assets/mystery_box/arrow_back.png') }}" alt="Back" style="height: 24px;" />
                    </a>
                    <h2 class="fw-bold mb-0 text-center flex-grow-1">
                        @if ($mode == 'Budget')
                            {{ __('mysterybox.chooseYourBudget') }}
                        @elseif ($mode == 'Mood')
                            {{ __('mysterybox.chooseYourMood') }}
                        @else
                            Done!
                        @endif
                    </h2>
                    <div style="width: 24px;"></div>
                </div>

                {{-- Progress Bar (mobile) --}}
                <div class="d-md-none w-100 mb-3 progress-bar-mobile">
                    <div class="d-flex justify-content-center align-items-center flex-wrap">
                        <div class="step text-center d-flex flex-column align-items-center">
                            <div
                                class="circle {{ $mode == 'Budget' || $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">
                                1</div>
                            <div class="label">{{ __('mysterybox.setBudget') }}</div>
                        </div>
                        <div class="line_completed"></div>
                        <div class="step text-center d-flex flex-column align-items-center">
                            <div class="circle {{ $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">2</div>
                            <div class="label">{{ __('mysterybox.setMood') }}</div>
                        </div>
                        <div class="line_completed {{ $mode == 'Done' ? '' : 'line_pending' }}"></div>
                        <div class="step text-center d-flex flex-column align-items-center">
                            <div class="circle {{ $mode == 'Done' ? 'active' : '' }}">3</div>
                            <div class="label">{{ __('mysterybox.done') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Mood Title (mobile) --}}
                @if ($mode == 'Mood')
                    <div class="d-md-none text-center mb-3 mt-1">
                        <h5 class="fw-bold">Mood</h5>
                        <span class="mood_description text-muted small mt-2">{!! __('mysterybox.descriptionMood') !!}</span>
                    </div>
                @elseif ($mode == 'Budget')
                    <div class="d-md-none text-center mb-3 mt-1">
                        <h5 class="fw-bold">Budget</h5>
                        <span class="mood_description text-muted small mt-2">{!! __('mysterybox.descriptionBudget') !!}</span>
                    </div>
                @endif

                {{-- Mystery Box --}}
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <div class="text-center mysterybox-wrapper">
                        <img id="mysteryBoxImage" class="mystery_box"
                            src= "{{ asset('assets/mystery_box/mysterybox_hitam.png') }}" alt="Mystery Box" />
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="right-section col-12 col-md-6 mt-3 d-flex flex-column position-relative">
                {{-- Progress Bar (desktop) --}}
                <div class="d-none d-md-block">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="step text-center d-flex flex-column align-items-center">
                            <div
                                class="circle {{ $mode == 'Budget' || $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">
                                1</div>
                            <div class="label mt-1">{{ __('mysterybox.setBudget') }}</div>
                        </div>
                        <div class="line_completed"></div>
                        <div class="step text-center d-flex flex-column align-items-center">
                            <div class="circle {{ $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">2</div>
                            <div class="label mt-1">{{ __('mysterybox.setMood') }}</div>
                        </div>
                        <div class="{{ $mode == 'Done' ? 'line_completed' : 'line_pending' }}"></div>
                        <div class="step text-center d-flex flex-column align-items-center">
                            <div class="circle {{ $mode == 'Done' ? 'active' : '' }}">3</div>
                            <div class="label mt-1">{{ __('mysterybox.done') }}</div>
                        </div>
                    </div>

                    <div class="text-center" style="margin-top: 40px;">
                        @if ($mode == 'Mood')
                            <h5 class="fw-bold">Mood</h5>
                            <p class="text-muted small mt-2">
                                {!! __('mysterybox.descriptionMood') !!}
                            </p>
                        @elseif ($mode == 'Budget')
                            <h5 class="fw-bold">Budget</h5>
                            <p class="text-muted small mt-2">
                                {!! __('mysterybox.descriptionBudget') !!}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Content Area (Budget & Mood) --}}
                <div class="main-content-area flex-grow-1 d-flex flex-column position-relative">
                    {{-- Step 1: Budget --}}
                    @if ($mode == 'Budget')
                        <form method="POST" action="{{ route('set-budget') }}" id="budgetForm"
                            class="w-100 flex-grow-1 d-flex flex-column align-items-center">
                            @csrf
                            @php
                                $budgets = [
                                    'Rp 25.000,00',
                                    'Rp 50.000,00',
                                    'Rp 75.000,00',
                                    'Rp 100.000,00',
                                    'Rp 125.000,00',
                                    'Rp 150.000,00',
                                ];
                            @endphp
                            <div class="mood-wrapper mb-2 w-1000 d-grid"
                                style="grid-template-columns: repeat(2, 1fr); gap: 20px;">
                                @foreach ($budgets as $budgetOption)
                                    <div class="mood_box budget_box" data-value="{{ $budgetOption }}">
                                        {{ $budgetOption }}
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="budget" id="selectedBudget" required>
                        </form>
                        <div class="next-button-wrapper">
                            <button type="submit" class="next_button"
                                form="budgetForm">{{ __('mysterybox.next') }}</button>
                        </div>

                        {{-- Step 2: Mood --}}
                    @elseif ($mode == 'Mood')
                        <form method="POST" action="{{ route('set-mood') }}" id="moodForm"
                            class="w-100 flex-grow-1 d-flex flex-column align-items-center">
                            @csrf
                            @php
                                $moods = [
                                    ['label' => __('mysterybox.romantic'), 'img' => 'mysterybox_pink.png', 'id' => 1],
                                    ['label' => __('mysterybox.mysterious'), 'img' => 'mysterybox_ungu.png', 'id' => 2],
                                    ['label' => __('mysterybox.funny'), 'img' => 'mysterybox_biru.png', 'id' => 3],
                                    ['label' => __('mysterybox.brave'), 'img' => 'mysterybox_merah.png', 'id' => 4],
                                    ['label' => __('mysterybox.calm'), 'img' => 'mysterybox_hijau.png', 'id' => 5],
                                    ['label' => __('mysterybox.happy'), 'img' => 'mysterybox_kuning.png', 'id' => 6],
                                ];
                            @endphp
                            <div class="mood-wrapper mb-2 w-1000 d-grid"
                                style="grid-template-columns: repeat(2, 1fr); gap: 20px;">
                                @foreach ($moods as $mood)
                                    <div class="mood_box" data-img="{{ asset('assets/mystery_box/' . $mood['img']) }}"
                                        data-value="{{ $mood['label'] }}" data-id="{{ $mood['id'] }}">
                                        {{ $mood['label'] }}
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="mood" id="selectedMood" required>
                            {{-- Input hidden 'mystery_box_id' DIHAPUS karena ID Mood tidak relevan untuk pencarian MysteryBox di backend --}}
                            {{-- <input type="hidden" name="mystery_box_id" id="selectedMysteryBoxId"> --}}
                        </form>
                        <div class="next-button-wrapper">
                            {{-- BUTTON ADD TO CART BARU, SESUAI REFERENSI --}}
                            <button type="submit"
                                class="next_button add-to-cart-button d-flex align-items-center justify-content-center"
                                form="moodForm">
                                <i class="bi bi-cart"></i>
                                <div class="addcart ms-2">{{ __('collection.addToCart') }}</div>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 p-4 text-center">
                <div class="warning-icon mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none"
                        viewBox="0 0 64 64">
                        <rect width="64" height="64" rx="12" fill="#dc3545" />
                        <path stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
                            d="M20 20l24 24M44 20L20 44" />
                    </svg>
                </div>
                <h4 class="fw-bold mb-2">{{ __('mysterybox.failed') }}</h4>
                <p class="mb-4">{{ __('mysterybox.chooseOptionFirst') }}</p>
                <button type="button" class="btn btn-danger rounded-pill px-4"
                    data-bs-dismiss="modal">{{ __('mysterybox.close') }}</button>
            </div>
        </div>
    </div>

    {{-- MODAL DONE (SUCCESS ADD TO CART) - DIADOP DARI collection_detail --}}
    <div class="modal fade" id="successAddToCartModal" tabindex="-1" aria-labelledby="successAddToCartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="successAddToCartModalLabel">Congratulations!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column align-items-center">
                    <div>
                        <p class="text-center">
                            Horeyyy, Snack Bouquet kamu sudah berhasil dibuat dan masuk ke keranjang!!!
                        </p>
                    </div>

                    <div class="d-flex justify-content-center w-100">
                        <a href="{{ route('cart.index', ['id_user' => Auth::id(), 'slug' => Str::slug(Auth::user()->name)]) }}"
                            class="btn btn-warning">Lihat keranjang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END MODAL DONE --}}

    <p id="chooseBudgetText" class="d-none">{{ __('mysterybox.chooseBudgetFirst') }}</p>
    <input type="hidden" id="id_user" value="{{ Auth::user()->id }}">
    <input type="hidden" id="slug" value="{{ Str::slug(Auth::user()->name) }}">
@endsection
