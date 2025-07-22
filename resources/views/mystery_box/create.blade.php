@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/mysterybox.css') }}" />
@endsection

@section('script')
    <script src="{{ asset('js/mysterybox.js') }}"></script>
@endsection

@section('content')

    <div class="container-fluid min-vh-100 d-flex flex-column flex-md-row mt-5" id="mysteryboxContainer" data-mode="{{ $mode }}">

    <div class="container-fluid d-flex flex-column flex-md-row" data-mode="{{ $mode }}">

        {{-- LEFT SIDE --}}
        <div class="left-section col-12 col-md-6 p-4 d-flex flex-column">
            {{-- Header --}}
            <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                <a href="{{route('home')}}" id="backBtn">
                    <img src="{{ asset('assets/mystery_box/arrow_back.png') }}" alt="Back" style="height: 24px;" />
                </a>
                <h2 class="fw-bold mb-0 text-center flex-grow-1">
                    @if ($mode == 'Budget') Choose your Budget
                    @elseif ($mode == 'Mood') Choose your Mood
                    @else Done!
                    @endif
                </h2>
                <div style="width: 24px;"></div>
            </div>

            {{-- Progress Bar (mobile only) --}}
            <div class="d-md-none w-100 mb-3 progress-bar-mobile">
                <div class="d-flex justify-content-center align-items-center flex-wrap">
                    <div class="step text-center d-flex flex-column align-items-center">
                        <div class="circle {{ $mode == 'Budget' || $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">1</div>
                        <div class="label">Set Budget</div>
                    </div>
                    <div class="line_completed"></div>
                    <div class="step text-center d-flex flex-column align-items-center">
                        <div class="circle {{ $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">2</div>
                        <div class="label">Set Mood</div>
                    </div>
                    <div class="line_completed {{ $mode == 'Done' ? '' : 'line_pending' }}"></div>
                    <div class="step text-center d-flex flex-column align-items-center">
                        <div class="circle {{ $mode == 'Done' ? 'active' : '' }}">3</div>
                        <div class="label">Done</div>
                    </div>
                </div>
            </div>

            {{-- Mood Title (mobile only) --}}
            @if ($mode == 'Mood')
            <div class="d-md-none text-center mb-3 mt-1">
                <h5 class="fw-bold">Mood</h5>
                <span class="mood_description text-muted small mt-2">Note : Mood akan menentukan warna dari snack di dalam mystery box</span>
            </div>
            @elseif ($mode == 'Budget')
            <div class="d-md-none text-center mb-3 mt-1">
                <h5 class="fw-bold">Budget</h5>
                <span class="mood_description text-muted small mt-2">Note : Semakin besar budget maka akan semakin banyak juga snack di dalam mystery box</span>
            </div>
            @endif

            {{-- Mystery Box --}}
            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                <div class="text-center mysterybox-wrapper">
                    <img
                        id="mysteryBoxImage"
                        class="mystery_box"
                        src= "{{ asset('assets/mystery_box/mysterybox_hitam.png') }}"
                        alt="Mystery Box"
                    />
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="right-section col-12 col-md-6 p-4 d-flex flex-column position-relative">
            {{-- Progress Bar (desktop only) --}}
            <div class="d-none d-md-block">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="step text-center d-flex flex-column align-items-center">
                        <div class="circle {{ $mode == 'Budget' || $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">1</div>
                        <div class="label mt-1">Set Budget</div>
                    </div>
                    <div class="line_completed"></div>
                    <div class="step text-center d-flex flex-column align-items-center">
                        <div class="circle {{ $mode == 'Mood' || $mode == 'Done' ? 'active' : '' }}">2</div>
                        <div class="label mt-1">Set Mood</div>
                    </div>
                    <div class="{{ $mode == 'Done' ? 'line_completed' : 'line_pending' }}"></div>
                    <div class="step text-center d-flex flex-column align-items-center">
                        <div class="circle {{ $mode == 'Done' ? 'active' : '' }}">3</div>
                        <div class="label mt-1">Done</div>
                    </div>
                </div>

                <div class="text-center" style="margin-top: 40px;">
                    @if ($mode == 'Mood')
                    <h5 class="fw-bold">Mood</h5>
                    <p class="text-muted small mt-2">
                        Note : Mood akan menentukan warna dari snack <br />
                        di dalam mystery box
                    </p>
                    @elseif ($mode == 'Budget')
                    <h5 class="fw-bold">Budget</h5>
                    <p class="text-muted small mt-2">
                        Note : Semakin besar budget maka akan semakin <br />
                        banyak juga snack di dalam mystery box
                    </p>
                    @endif
                </div>
            </div>

            {{-- Content Area (Budget / Mood) --}}
            <div class="flex-grow-1 d-flex flex-column align-items-center">
                {{-- Step 1: Budget --}}
                @if ($mode == 'Budget')
                <form method="POST" action="{{ route('set-budget') }}" id="budgetForm" class="w-100 d-flex flex-column align-items-center">
                    @csrf
                    @php
                        $budgets = [
                            'Rp 25.000,00', 'Rp 50.000,00', 'Rp 75.000,00', 'Rp 100.000,00', 'Rp 125.000,00', 'Rp 150.000,00'
                        ];
                    @endphp
                    <div class="mood-wrapper mb-2 w-1000 d-grid" style="grid-template-columns: repeat(2, 1fr); gap: 30px;">
                        @foreach ($budgets as $budgetOption)
                        <div class="mood_box budget_box" data-value="{{ $budgetOption }}">
                            {{ $budgetOption }}
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="budget" id="selectedBudget" required>
                    <div class="mobile-button-wrapper position-absolute bottom-0 end-0 p-">
                        <button type="submit" class="next_button">Next</button>
                    </div>
                </form>

                <!-- Warning Pop Up -->
                <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 p-4 text-center">
                            <div class="warning-icon mx-auto mb-3">
                                <!-- Kotak merah dengan tanda silang -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" viewBox="0 0 64 64">
                                    <rect width="64" height="64" rx="12" fill="#dc3545"/>
                                    <path stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" d="M20 20l24 24M44 20L20 44"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-2">Failed</h4>
                            <p class="mb-4">Please choose a budget first!</p>
                            <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Mood --}}
                @elseif ($mode == 'Mood')
                <form method="POST" action="{{ route('set-mood') }}" id="moodForm" class="w-100 d-flex flex-column align-items-center">
                    @csrf
                    @php
                        $moods = [
                            ['label' => 'Romantis', 'img' => 'mysterybox_pink.png'],
                            ['label' => 'Misterius', 'img' => 'mysterybox_ungu.png'],
                            ['label' => 'Lucu', 'img' => 'mysterybox_biru.png'],
                            ['label' => 'Berani', 'img' => 'mysterybox_merah.png'],
                            ['label' => 'Tenang', 'img' => 'mysterybox_hijau.png'],
                            ['label' => 'Bahagia', 'img' => 'mysterybox_kuning.png'],
                        ];
                    @endphp
                    <div class="mood-wrapper mb-2 w-1000 d-grid" style="grid-template-columns: repeat(2, 1fr); gap: 30px;">
                        @foreach ($moods as $mood)
                        <div class="mood_box" data-img="{{ asset('assets/mystery_box/' . $mood['img']) }}" data-value="{{ $mood['label'] }}">
                            {{ $mood['label'] }}
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="mood" id="selectedMood" required>
                    <div class="mobile-button-wrapper position-absolute bottom-0 end-0 p-4">
                        <button type="submit" class="next_button">Next</button>
                    </div>
                </form>

                <!-- Warning Pop Up -->
                <div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 p-4 text-center">
                            <div class="warning-icon mx-auto mb-3">
                                <!-- Kotak merah dengan tanda silang -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" viewBox="0 0 64 64">
                                    <rect width="64" height="64" rx="12" fill="#dc3545"/>
                                    <path stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" d="M20 20l24 24M44 20L20 44"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-2">Failed</h4>
                            <p class="mb-4">Please choose a mood first!</p>
                            <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>

                <!-- Pop Up Success-->
                <div class="modal fade" id="doneModal" tabindex="-1" aria-labelledby="doneModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 p-4 text-center">
                    <div class="success-icon mx-auto mb-3">
                        <!-- Kotak hijau dengan ceklis -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" viewBox="0 0 64 64">
                        <rect width="64" height="64" rx="12" fill="#28a745"/>
                        <path stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" d="M18 34l10 10 18-24"/>
                        </svg>
                    </div>
                    <h4 class="fw-bold mb-2">Success</h4>
                    <p class="mb-4">Mystery box has been added to cart!</p>
                    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
                </div>
                @endif
            </div>
        </div>
    </div>

@endsection
