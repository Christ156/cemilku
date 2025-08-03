@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customize_tower.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/customize_tower.js') }}"></script>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row d-flex">
            <div class="col-lg-6 col-12">
                <div class="row d-flex">
                    <a href="{{ route('home') }}"
                        class="d-lg-block d-none col-0 col-lg-2 d-flex align-items-center justify-content-center decoration-none">
                        <i class="bi bi-arrow-left-circle-fill" style="color: #542828; font-size: 1.75rem;"></i></a>
                    <div class="col-lg-10 col-12 d-flex align-items-center justify-content-lg-start justify-content-center">
                        <p class="fs-lg-1 fs-4 fw-bold m-0"style="color: #542828;">{{ __('tower.createTower') }}</p>
                    </div>
                </div>

                <div class="position-relative d-flex flex-column align-items-center justify-content-center p-3">
                    <div class="preview-tower-1" id="tower-layer-1">
                        <img class="w-100" src="{{ asset('assets/tower_layer_1/unselect_layer.png') }}" alt=""
                            id="preview-tower-1" onclick="setCurrentLayer(1)">
                    </div>
                    <div class="preview-tower-2" id="tower-layer-2">
                        <img class="w-100" src="{{ asset('assets/tower_layer_2/unselect_layer.png') }}" alt=""
                            id="preview-tower-2" onclick="setCurrentLayer(2)">
                    </div>
                    <div class="preview-tower-3" id="tower-layer-3">
                        <img class="w-100" src="{{ asset('assets/tower_layer_3/unselect_layer.png') }}" alt=""
                            id="preview-tower-3" onclick="setCurrentLayer(3)">
                    </div>
                    <div class="preview-tower-4" id="tower-layer-4">
                        <img class="w-100" src="{{ asset('assets/tower_layer_4/unselect_layer.png') }}" alt=""
                            id="preview-tower-4" onclick="setCurrentLayer(4)">
                    </div>
                    <div class="decoration-tower" id="tower-decor">
                        <img class="w-100" src="{{ asset('assets/decoration/1.png') }}" alt=""
                            id="preview-tower-decor">
                    </div>
                </div>

                <div class="w-100 d-flex justify-content-center py-2">
                    <p id="warning-tower" class="m-0 text-danger text-center"></p>
                </div>
            </div>

            {{-- Right content --}}
            <div class="col-lg-6 col-12">
                <div class="w-100 d-flex justify-content-center mt-3">
                    <div class="row w-lg-20 w-50 d-flex">
                        <div class="col-2 p-0">
                            <div class="progress-node" id="progress-node-1">
                                1</div>
                        </div>
                        <div class="col-3 p-0 d-flex align-items-center">
                            <div class="w-100 bg-dark" style="height: 5px"></div>
                        </div>
                        <div class="col-2 p-0">
                            <div class="progress-node" id="progress-node-2">
                                2</div>
                        </div>
                        <div class="col-3 p-0 d-flex align-items-center">
                            <div class="w-100 bg-dark" style="height: 5px"></div>
                        </div>
                        <div class="col-2 p-0">
                            <div class="progress-node" id="progress-node-3">
                                3</div>
                        </div>
                    </div>
                </div>

                <div class="w-100 d-flex justify-content-center mb-3">
                    <div class="row w-lg-25 w-50 d-flex justify-content-between">
                        <div class="col-3 p-0">
                            <p class="m-0">{{ __('tower.setLayer') }}</p>
                        </div>
                        <div class="col p-0 text-center">
                            <p class="m-0">{{ __('tower.setDecoration') }}</p>
                        </div>
                        <div class="col-3 p-0 text-end">
                            <p class="m-0">{{ __('tower.done') }}</p>
                        </div>
                    </div>
                </div>

                <div class="w-100 overflow-hidden">
                    <div class="d-flex wrapper-customize-menu" id="customize-menu">
                        <div class="py-3" id="set-layer">
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('tower.chooseLayer') }}</p>
                                </div>
                            </div>

                            <div class="w-100 p-5 row">
                                <div class="col-4 p-2">
                                    <div class="card w-100 ratio ratio-1x1 text-center" onclick="previewLayer(2)">
                                        <img class="object-fit-scale w-75 h-75 m-3"
                                            src="{{ asset('assets/tower_selection/preview-tower-layer-2.png') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="col-4 p-2">
                                    <div class="card w-100 ratio ratio-1x1 text-center" onclick="previewLayer(3)">
                                        <img class="object-fit-scale w-75 h-75 m-3"
                                            src="{{ asset('assets/tower_selection/preview-tower-layer-3.png') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="col-4 p-2">
                                    <div class="card w-100 ratio ratio-1x1 text-center" onclick="previewLayer(4)">
                                        <img class="object-fit-scale w-75 h-75 m-3"
                                            src="{{ asset('assets/tower_selection/preview-tower-layer-4.png') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="py-3 position-relative" id="set-snack">
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('tower.chooseSnack') }}</p>
                                </div>
                            </div>

                            {{-- Snack layer 1 --}}
                            <div id="snack-layer-1" class="list-snack overflow-scroll overflow-x-hidden mt-3">
                                <div class="row d-flex p-3">
                                    @forelse ($snack as $s)
                                        @if ($s->layer == 1)
                                            <div class="col-4 p-2">
                                                <div class="card">
                                                    <div class="card-img-top ratio ratio-1x1">
                                                        <img class="object-fit-scale"
                                                            src="{{ asset('assets/snack_items/' . $s->snack->image) }}"
                                                            alt="Title" id="list-snack" />
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title m-0">
                                                            {{ Str::limit($s->snack->name, 12, '...') }}</h5>
                                                        <p class="card-text fs-bold m-0">
                                                            Rp{{ Str::currency($s->snack->price * 10) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 10 }}, {{ $s->id_snack }})">{{ __('tower.add') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                    <div class="w-100" style="height: 60px">
                                    </div>
                                </div>
                            </div>

                            {{-- Snack layer 2 --}}
                            <div id="snack-layer-2" class="list-snack overflow-scroll overflow-x-hidden mt-3"
                                style="display: none">
                                <div class="row d-flex p-3">
                                    @forelse ($snack as $s)
                                        @if ($s->layer == 2)
                                            <div class="col-4 p-2">
                                                <div class="card">
                                                    <div class="card-img-top ratio ratio-1x1">
                                                        <img class="object-fit-scale"
                                                            src="{{ asset('assets/snack_items/' . $s->snack->image) }}"
                                                            alt="Title" id="list-snack" />
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title m-0">
                                                            {{ Str::limit($s->snack->name, 10, '...') }}</h5>
                                                        <p class="card-text fs-bold m-0">
                                                            Rp{{ Str::currency($s->snack->price * 10) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 10 }}, {{ $s->id_snack }})">{{ __('tower.add') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                    <div class="w-100" style="height: 60px">
                                    </div>
                                </div>
                            </div>

                            {{-- Snack layer 3 --}}
                            <div id="snack-layer-3" class="list-snack overflow-scroll overflow-x-hidden mt-3"
                                style="display: none">
                                <div class="row d-flex p-3">
                                    @forelse ($snack as $s)
                                        @if ($s->layer == 3)
                                            <div class="col-4 p-2">
                                                <div class="card">
                                                    <div class="card-img-top ratio ratio-1x1">
                                                        <img class="object-fit-scale"
                                                            src="{{ asset('assets/snack_items/' . $s->snack->image) }}"
                                                            alt="Title" id="list-snack" />
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title m-0">
                                                            {{ Str::limit($s->snack->name, 10, '...') }}</h5>
                                                        <p class="card-text fs-bold m-0">
                                                            Rp{{ Str::currency($s->snack->price * 10) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 10 }}, {{ $s->id_snack }})">{{ __('tower.add') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                    <div class="w-100" style="height: 60px">
                                    </div>
                                </div>
                            </div>

                            {{-- Snack layer 4 --}}
                            <div id="snack-layer-4" class="list-snack overflow-scroll overflow-x-hidden mt-3"
                                style="display: none">
                                <div class="row d-flex p-3">
                                    @forelse ($snack as $s)
                                        @if ($s->layer == 4)
                                            <div class="col-4 p-2">
                                                <div class="card">
                                                    <div class="card-img-top ratio ratio-1x1">
                                                        <img class="object-fit-scale"
                                                            src="{{ asset('assets/snack_items/' . $s->snack->image) }}"
                                                            alt="Title" id="list-snack" />
                                                    </div>
                                                    <div class="card-body">
                                                        <h5 class="card-title m-0">
                                                            {{ Str::limit($s->snack->name, 10, '...') }}</h5>
                                                        <p class="card-text fs-bold m-0">
                                                            Rp{{ Str::currency($s->snack->price * 10) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 10 }}, {{ $s->id_snack }})">{{ __('tower.add') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                    <div class="w-100" style="height: 60px">
                                    </div>
                                </div>
                            </div>

                            <div class="floating-control-button">
                                <div class="col d-flex align-items-center">
                                    <p class="m-0 fs-2 fw-bold">Rp<span id="temp_price1">0</span></p>
                                </div>
                                <div class="col d-flex align-items-center justify-content-end">
                                    <button disabled id="next-progress-1" class="btn btn-warning px-3"
                                        onclick="controlProgress('next')">{{ __('tower.next') }}</button>
                                </div>
                            </div>
                        </div>

                        <div class="position-relative py-3" id="set-decoration">
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('tower.chooseDecoration') }}</p>
                                </div>
                            </div>

                            <div class="list-snack overflow-scroll overflow-x-hidden mt-3">
                                <div class="row d-flex p-3">
                                    <div class="col-4 p-2">
                                        <div class="card ">
                                            <div class="card-img-top ratio ratio-1x1">
                                                <img class="object-fit-scale"
                                                    src="{{ asset('assets/decoration/not-use.png') }}" alt="Title"
                                                    id="list-snack" />
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title m-0">{{ Str::limit('No decoration', 13, '...') }}
                                                </h5>
                                                <p class="card-text fs-bold m-0">Rp{{ Str::currency(0) }}</p>
                                            </div>
                                            <div class="card-footer d-flex justify-content-center">
                                                <button class="btn btn-warning px-lg-5 px-2" id="add-snack--1"
                                                    onclick="previewDecoration('no decoration', 0, 0)">{{ __('tower.add') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                    @forelse ($decoration as $d)
                                        <div class="col-4 p-2">
                                            <div class="card ">
                                                <div class="card-img-top ratio ratio-1x1">
                                                    <img class="object-fit-scale"
                                                        src="{{ asset('assets/decoration/' . $d->image) }}"
                                                        alt="Title" id="list-snack" />
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="card-title m-0">{{ Str::limit($d->name, 13, '...') }}</h5>
                                                    <p class="card-text fs-bold m-0">Rp{{ Str::currency($d->price) }}</p>
                                                </div>
                                                <div class="card-footer d-flex justify-content-center">
                                                    <button class="btn btn-warning px-lg-5 px-2"
                                                        id="add-snack-{{ $d->id }}"
                                                        onclick="previewDecoration('{{ $d->image }}', {{ $d->price }}, {{ $d->id }})">{{ __('tower.add') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                    @endforelse
                                    <div class="w-100" style="height: 60px">
                                    </div>
                                </div>
                            </div>
                            <div class="floating-control-button">
                                <div class="col d-flex align-items-center">
                                    <p class="m-0 fs-2 fw-bold">Rp<span id="temp_price2">0</span></p>
                                </div>
                                <div class="col d-flex align-items-center justify-content-end">
                                    <button class="btn btn-warning px-3 me-2"
                                        onclick="controlProgress('prev')">{{ __('tower.back') }}</button>
                                    <button class="btn btn-warning px-3"
                                        onclick="controlProgress('next')">{{ __('tower.next') }}</button>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('customer-tower-bouquet.store', ['type' => 'tower']) }}" method="POST"
                            class="position-relative py-3" id="confirmation-customize">
                            @csrf
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('tower.confirmation') }}</p>
                                </div>
                            </div>

                            <div class="list-snack overflow-scroll overflow-x-hidden mt-3">
                                <div class="row d-flex justify-content-center align-items-center p-3">
                                    <p class="fs-5 fw-bold m-0 text-center">{{ __('tower.areYouSure') }}</p>
                                </div>
                                <div class="mx-3">
                                    <label for="" class="form-label">{{ __('tower.giveName') }}</label>
                                    <input type="text" name="name" id="customize-name" class="form-control"
                                        placeholder="" />
                                    <p class="m-0 text-danger" id="warning-tower-name"></p>
                                </div>

                                <input type="hidden" name="price" id="customize-price" value="">
                                <input type="hidden" name="layer" id="layer-selected" value="">
                                <input type="hidden" name="snack_1" value="0" id="snack-1">
                                <input type="hidden" name="snack_2" value="0" id="snack-2">
                                <input type="hidden" name="snack_3" value="0" id="snack-3">
                                <input type="hidden" name="snack_4" value="0" id="snack-4">
                                <input type="hidden" name="decoration" value="" id="decoration">
                            </div>
                            <div class="floating-control-button">
                                <div class="col d-flex align-items-center">
                                    <p class="m-0 fs-2 fw-bold">Rp<span id="temp_price3">0</span></p>
                                </div>
                                <div class="col d-flex align-items-center justify-content-end">
                                    <button class="btn btn-warning px-3 me-2" type="button"
                                        onclick="controlProgress('prev')">{{ __('tower.back') }}</button>
                                    <button id="finish-customize" class="btn btn-warning px-3" type="button"
                                        onclick="checkCustomizeName()">{{ __('tower.finish') }}</button>
                                </div>
                            </div>

                            {{-- MODAL CONFIRMATION --}}
                            <div class="modal fade color_secondary" id="confirmationBouquet" tabindex="-1"
                                aria-labelledby="confirmationBouquetLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Congratulations</h1>
                                        </div>
                                        <div class="modal-body d-flex flex-column justify-content-center">
                                            <div>
                                                <p class="text-center">
                                                    Horeyyy, Snack Tower kamu sudah berhasil dibuat dan masuk ke
                                                    keranjang!!!
                                                </p>
                                            </div>
                                            <button type="submit" class="btn btn-warning">Lihat keranjang</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
