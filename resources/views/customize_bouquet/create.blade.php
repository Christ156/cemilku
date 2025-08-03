@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customize_bouquet.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/customize_bouquet.js') }}"></script>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row d-flex">
            <div class="col-lg-6 col-12">
                <div class="row d-flex">
                    <a href="{{ route('home') }}"
                        class="col-2 d-flex align-items-center justify-content-center decoration-none"><i
                            class="bi bi-arrow-left-circle-fill" style="color: #542828; font-size: 1.75rem;"></i></a>
                    <div class="col-10 d-flex align-items-center justify-content-lg-start justify-content-end">
                        <p class="fs-lg-1 fs-4 fw-bold m-0" style="color: #542828;">{{ __('bouquet.createYourBouquet') }}</p>
                    </div>
                </div>

                <div class="position-relative d-flex flex-column align-items-center justify-content-center p-3">
                    <div class="preview-bouquet-base" id="bouquet-base">
                        <img src="{{ asset('assets/bouquet_base/base.png') }}" alt="" id="preview-bouquet-base">
                    </div>
                    <div class="preview-bouquet-1" id="bouquet-layer-1">
                        <img src="{{ asset('assets/bouquet_Layer_1/unselect_layer.png') }}" alt=""
                            id="preview-bouquet-1" onclick="setCurrentLayer(1)">
                    </div>
                    <div class="preview-bouquet-2" id="bouquet-layer-2">
                        <img src="{{ asset('assets/bouquet_layer_2&3/unselect_layer.png') }}" alt=""
                            id="preview-bouquet-2" onclick="setCurrentLayer(2)">
                    </div>
                    <div class="preview-bouquet-3" id="bouquet-layer-3">
                        <img src="{{ asset('assets/bouquet_layer_2&3/unselect_layer.png') }}" alt=""
                            id="preview-bouquet-3" onclick="setCurrentLayer(3)">
                    </div>
                    <div class="preview-bouquet-4" id="bouquet-layer-4">
                        <img src="{{ asset('assets/bouquet_layer_4/unselect_layer.png') }}" alt=""
                            id="preview-bouquet-4" onclick="setCurrentLayer(4)">
                    </div>
                    <div class="preview-bouquet-decor" id="bouquet-decor"></div>
                </div>
                <div class="w-100 d-flex justify-content-center py-2">
                    <p id="warning-bouquet" class="m-0 text-danger text-center"></p>
                </div>
            </div>

            {{-- Right content --}}
            <div class="col-lg-6 col-12">
                <div class="w-100 d-flex justify-content-center mt-3">
                    <div class="row w-lg-25 w-50 d-flex">
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
                            <p class="m-0">{{ __('bouquet.setBase') }}</p>
                        </div>
                        <div class="col p-0 text-center">
                            <p class="m-0">{{ __('bouquet.setLayer') }}</p>
                        </div>
                        <div class="col-3 p-0 text-end">
                            <p class="m-0">{{ __('bouquet.done') }}</p>
                        </div>
                    </div>
                </div>

                <div class="w-100 overflow-hidden">
                    <div class="d-flex wrapper-customize-menu" id="customize-menu">
                        <div class="py-3 d-flex flex-column align-items-center" id="set-base">
                            <div class="w-100 d-flex justify-content-center">
                                <div class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('bouquet.chooseBase') }}</p>
                                </div>
                            </div>

                            <div class="w-100 p-5 row">
                                @for ($i = 0; $i <= 3; $i++)
                                    <div class="col-3 p-2">
                                        <?php $base_bouquet = ['Blue', 'Black', 'Red', 'Purple']; ?>
                                        <div class="card w-100 p-2"
                                            onclick="changePreview('{{ $base_bouquet[$i] }}', 0, 0, 0)">
                                            <img class="card-img-top"
                                                src="{{ asset('assets/bouquet_base/' . $base_bouquet[$i] . '.png') }}"
                                                alt="Title" />
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="py-3" id="set-layer">
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('bouquet.chooseLayer') }}</p>
                                </div>
                            </div>

                            <div class="w-100 p-5">
                                <div class="w-100 btn btn-warning mb-2" onclick="previewLayer(2)">2 Layer</div>
                                <div class="w-100 btn btn-warning mb-2" onclick="previewLayer(3)">3 Layer</div>
                                <div class="w-100 btn btn-warning mb-2" onclick="previewLayer(4)">4 Layer</div>
                            </div>
                        </div>

                        <div class="py-3 position-relative" id="set-snack">
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('bouquet.chooseSnack') }}</p>
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
                                                            Rp{{ Str::currency($s->snack->price * 5) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 5 }}, {{ $s->id_snack }}, 1)">{{ __('bouquet.add') }}</button>
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
                                                            Rp{{ Str::currency($s->snack->price * 5) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 5 }}, {{ $s->id_snack }}, 2)">{{ __('bouquet.add') }}</button>
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
                                                            Rp{{ Str::currency($s->snack->price * 5) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 5 }}, {{ $s->id_snack }}, 3)">{{ __('bouquet.add') }}</button>
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
                                                            Rp{{ Str::currency($s->snack->price * 3) }}</p>
                                                    </div>
                                                    <div class="card-footer d-flex justify-content-center">
                                                        <button class="btn btn-warning px-lg-5 px-2"
                                                            id="add-snack-{{ $s->id_snack }}"
                                                            onclick="changePreview('{{ $s->snack->image }}', {{ $s->snack->price * 5 }}, {{ $s->id_snack }}, 4)">{{__('bouquet.add')}}</button>
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
                                    <button type="button" class="btn btn-warning px-3" id="next-progress"
                                        onclick="controlProgress('next')" disabled>{{ __('bouquet.next') }}</button>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('customer-tower-bouquet.store', ['type' => 'bouquet']) }}" method="POST"
                            class="position-relative py-3" id="confirmation-customize">
                            @csrf
                            <div class="w-100 d-flex justify-content-center">
                                <div href="" class="bg-warning p-2 w-50 rounded text-center">
                                    <p class="fs-5 fw-bold m-0">{{ __('bouquet.confirmation') }}</p>
                                </div>
                            </div>

                            <div class="list-snack overflow-scroll overflow-x-hidden mt-3">
                                <div class="row d-flex justify-content-center align-items-center p-3">
                                    <p class="fs-5 fw-bold m-0 text-center">{{ __('bouquet.confirmMessage') }}</p>
                                </div>
                                <div class="mx-3">
                                    <label for="" class="form-label">{{ __('bouquet.confirmName') }}</label>
                                    <input type="text" name="name" id="customize-name" class="form-control"
                                        placeholder=""/>
                                    <p class="m-0 text-danger" id="warning-bouquet-name"></p>
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
                                    <p class="m-0 fs-2 fw-bold">Rp<span id="temp_price2">0</span></p>
                                </div>
                                <div class="col d-flex align-items-center justify-content-end">
                                    <button class="btn btn-warning px-3 me-2" type="button"
                                        onclick="controlProgress('prev')">{{ __('bouquet.back') }}</button>
                                    <button id="finish-customize" onclick="checkCustomizeName()"
                                        class="btn btn-warning px-3" type="button"
                                        class="btn btn-primary">{{ __('bouquet.finish') }}</button>
                                </div>
                            </div>

                            {{-- MODAL CONFIRMATION --}}
                            <div class="modal fade" id="confirmationBouquet" tabindex="-1"
                                aria-labelledby="confirmationBouquetLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                {{ __('bouquet.congratulations') }}</h1>
                                        </div>
                                        <div class="modal-body d-flex flex-column justify-content-center">
                                            <div>
                                                <p class="text-center">
                                                    {{ __('bouquet.horey') }}
                                                </p>
                                            </div>
                                            <button type="submit"
                                                class="btn btn-warning">{{ __('bouquet.seeCart') }}</button>
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
