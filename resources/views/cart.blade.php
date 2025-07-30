@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/cart.js') }}" defer></script>
@endsection

@section('content')
    <div class="container mt-5" onchange="checkItemSelected({{ $carts->count() }}, {{ json_encode($carts->toArray()) }}, {{$count_address_active}})">
        <div class="row">
            <form
                action="{{ route('cart.destroy', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name ?? ''), 'count_items' => $carts->count()]) }}"
                method="POST" class="col-md-8">
                @csrf
                @method('DELETE')
                <div class="shipping-address-box-style-2 p-3 mb-3"
                    style="background-color: #FFF8E2; border-radius: 10px; padding: 15px;">
                    <h5 class="fw-bold" style="color: #52282A;">{{__('cart.shippingAddress')}}</h5>
                    <div class="d-flex align-items-start mb-2" id="shippingAddressDisplayContainer">
                        <p class="mb-0" id="currentShippingAddress" style="color: #52282A;">
                            {{-- Cumi Cumi Pak Kris Ikan Bakar Sambal Matan, Jalan Pakuan No. 3 Kel. Babakan Madang, Kab. Bogor,
                            Jawa Barat --}}
                            @if ($count_address_active == 0)
                                <p>{{__('cart.addressIsEmpty')}}</p>
                            @else
                                <div class="w-100 ps-2 my-2">
                                    <div class="w-100 d-flex justify-content-between">
                                        <p class="fw-bold m-0">{{ $address_active->label }}</p>
                                    </div>
                                    <p class="m-0 fw-bold fs-5">{{ $address_active->receiver_name }}</p>
                                    <p class="m-0">{{ $address_active->phone_number }}</p>
                                    <p class="m-0">{{ $address_active->address }},
                                        Kel.{{ $address_active->kelurahan_desa }}
                                        RT{{ $address_active->rt }}/RW{{ $address_active->rw }},
                                        Kel.{{ $address_active->kelurahan_desa }},
                                        Kec.{{ $address_active->kecamatan }}, Kab.{{ $address_active->kota_kabupater }},
                                        {{ $address_active->provinsi }}
                                        {{ $address_active->kode_pos }}</p>
                                </div>
                            @endif
                        </p>
                    </div>
                    @if ($count_address_active == 0 && $address->count() == 0)
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addressCartModal" class="btn btn-sm"
                            style="background-color: #FFF8E2; border: 1px solid #D1BB9E; color: #52282A; padding: 5px 15px; border-radius: 20px; font-weight: bold;">{{__('cart.addNewAddress')}}
                            </button>
                    @else
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addressCartModal" class="btn btn-sm"
                            style="background-color: #FFF8E2; border: 1px solid #D1BB9E; color: #52282A; padding: 5px 15px; border-radius: 20px; font-weight: bold;">{{__('cart.edit')}}</button>
                    @endif
                </div>

                <div class="select-all d-flex justify-content-between align-items-center">
                    <label class="mb-0">
                        <input type="checkbox" id="select_all"
                            onchange="allCheckboxCheck({{ $carts->count() }}, {{ json_encode($carts->toArray()) }})">
                        {{__('cart.selectAll')}}
                    </label>

                    <button type="submit" id="remove-btn" class="btn btn-link text-decoration-none" style="display: none">
                        {{__('cart.remove')}}
                    </button>
                </div>

                <div class="product-list mt-3" id="cart-product-list" onchange="allCheckTrue({{ $carts->count() }})">
                    @forelse($carts as $c)
                        <div class="product-item d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="product-checkbox me-2" id="checkbox_item{{ $c->id }}"
                                    name="cart_item_{{ $c->id }}"
                                    onchange="previewPrice({{ $c->total_price }}, {{ $c->id }})">
                                {{-- PERBAIKAN: Menggunakan gambar dari koleksi produk --}}
                                @if ($c->collection_id != null)
                                    <img src="{{ asset('assets/collections/' . $c->collection->image) }}" alt=""
                                        class="product-img">
                                @elseif($c->customize_id != null)
                                    @if ($c->customize->type == 'tower')
                                        <img src="{{ asset('assets/tower_selection/preview-tower-layer-4.png') }}"
                                            alt="" class="product-img">
                                    @else
                                        <img src="{{ asset('assets/bouquet_base/base.png') }}" alt=""
                                            class="product-img">
                                    @endif
                                @else
                                    <?php
                                        $image_mysteryBox = array(
                                            'Romantic'=>'mysterybox_pink.png',
                                            'Funny'=>'mysterybox_biru.png',
                                            'Calm'=>'mysterybox_hijau.png',
                                            'Mysterious'=>'mysterybox_ungu.png',
                                            'Brave'=>'mysterybox_merah.png',
                                            'Happy'=>'mysterybox_kuning.png',
                                        );
                                    ?>
                                    <img src="{{ asset('assets/mystery_box/'. $image_mysteryBox[$c->mysteryBox->mood]) }}" alt=""
                                        class="product-img">
                                @endif
                                <div class="ms-2">
                                    @if ($c->collection_id != null)
                                        <h6 class="fw-bold mb-0">{{ $c->collection->category }} |
                                            {{ $c->collection->name }}</h6>
                                    @elseif($c->customize_id != null)
                                        <h6 class="fw-bold mb-0">{{ $c->customize->name }} - {{__('cart.custom')}}
                                            {{ $c->customize->type }}</h6>
                                    @else
                                        <h6 class="fw-bold mb-0">{{ $c->mysteryBox->mood }} -
                                            {{ $c->mysteryBox->name }}</h6>
                                    @endif
                                    <small>{{ $c->quantity }} pcs</small>
                                </div>
                            </div>
                            <p class="mb-0 me-3">Rp{{ Str::currency($c->total_price) }}</p>
                        </div>
                    @empty
                        <p>{{__('cart.yourCartIsEmpty')}}</p>
                    @endforelse
                </div>


            </form>

            <div class="col-md-4">
                <form method="POST"
                    action="{{ route('cart.checkout', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name ?? '')]) }}"
                    class="summary-box p-3">
                    @csrf

                    <h5 class="fw-bold" style="color:#52282A;font-size:20px;">
                        {{__('cart.paymentMethod')}}
                    </h5>

                    <hr style="border-top:2px solid #52282A;">

                    <div class="form-check d-flex justify-content-between my-3">
                        <label class="form-check-label fs-5" for="payment_method_1"><img
                                src="{{ asset('assets/bank_logo/BCA.png') }}" alt="" width="30"
                                class="me-2">BCA Virtual Account</label>
                        <input class="form-check-input" type="radio" name="payment_method" value="BCA"
                            id="payment_method_1" />
                    </div>
                    <div class="form-check d-flex justify-content-between my-3">
                        <label class="form-check-label fs-5" for="payment_method_2"><img
                                src="{{ asset('assets/bank_logo/Mandiri.png') }}" alt="" width="30"
                                class="me-2">Mandiri Virtual Account</label>
                        <input class="form-check-input" type="radio" name="payment_method" value="Mandiri"
                            id="payment_method_2" />
                    </div>
                    <div class="form-check d-flex justify-content-between my-3">
                        <label class="form-check-label fs-5" for="payment_method_3"><img
                                src="{{ asset('assets/bank_logo/CimbNiaga.png') }}" alt="" width="30"
                                class="me-2">Cimb Niaga Virtual Account</label>
                        <input class="form-check-input" type="radio" name="payment_method" value="Cimb Niaga"
                            id="payment_method_3" />
                    </div>
                    <div class="form-check d-flex justify-content-between my-3">
                        <label class="form-check-label fs-5" for="payment_method_4"><img
                                src="{{ asset('assets/bank_logo/Danamon.png') }}" alt="" width="30"
                                class="me-2">Danamon Virtual Account</label>
                        <input class="form-check-input" type="radio" name="payment_method" value="Danamon"
                            id="payment_method_4" />
                    </div>

                    <h5 class="fw-bold mt-4" style="color:#52282A;font-size:20px;">
                        {{__('cart.orderSummary')}}
                    </h5>
                    <hr style="border-top:2px solid #52282A;">

                    <div class="d-flex justify-content-between">
                        <span>{{__('cart.totalPrice')}} (<span id="product-count">0</span> {{__('cart.product')}})</span>
                        <span>Rp<span id="total_price_cart">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{__('cart.shippingRegular')}}</span>
                        <span>Rp20.000</span>
                    </div>

                    <hr style="border-top:2px solid #52282A;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold" style="font-size:20px;">{{__('cart.total')}}</h5>
                        <p class="fw-bold mb-0" style="font-size:20px;">Rp<span id="total_price_with_ship">0</span></p>
                    </div>

                    <hr style="border-top:2px solid #52282A;">
                    <div class="d-flex justify-content-end">
                        <input type="hidden" name="total_price" id="total_price" value="0">
                        @forelse ($carts as $c)
                            <input type="hidden" name="item_cart_{{ $c->id }}"
                                id="item_cart_{{ $c->id }}" value="">
                        @empty
                        @endforelse
                        <button id="checkout_btn" type="submit" class="btn btn-checkout py-3 fw-bold" disabled>{{__('cart.buyNow')}}</button>
                    </div>
                </form>
            </div>
        </div> {{-- Penutup div container --}}

        <!-- Address Cart Modal -->
        <div class="modal fade" id="addressCartModal" tabindex="-1" aria-labelledby="addressCartModal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content color_secondary">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">{{__('cart.address')}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addAddressCartModal"
                            class="w-100 card border border-warning p-2 mb-2 color_secondary">
                            <p class="m-0 fw-bold">{{__('cart.addNewAddress')}}</p>
                        </button>
                        <div>
                            @if ($count_address_active != 0)
                                <div class="w-100 card border border-warning p-3 mb-2 bg-warning">
                                    <div class="w-100 d-flex justify-content-between">
                                        <p class="fw-bold m-0">{{ $address_active->label }}</p>
                                        <span class="badge bg-light text-dark">{{__('cart.primary')}}</span>
                                    </div>
                                    <p class="m-0 fw-bold fs-5">{{ $address_active->receiver_name }}</p>
                                    <p class="m-0">{{ $address_active->phone_number }}</p>
                                    <p class="m-0">{{ $address_active->address }},
                                        Kel.{{ $address_active->kelurahan_desa }}
                                        RT{{ $address_active->rt }}/RW{{ $address_active->rw }},
                                        Kel.{{ $address_active->kelurahan_desa }},
                                        Kec.{{ $address_active->kecamatan }}, Kab.{{ $address_active->kota_kabupater }},
                                        {{ $address_active->provinsi }}
                                        {{ $address_active->kode_pos }}</p>
                                </div>
                            @endif
                            @forelse ($address as $a)
                                <form
                                    action="{{ route('cart.primary.address', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name ?? '')]) }}"
                                    method="post">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="set-primary-address" value="{{ $a->id }}">
                                    <button type="submit" class="w-100 card border border-warning p-3 mb-2 text-start">
                                        <div class="w-100 d-flex">
                                            <p class="fw-bold m-0">{{ $a->label }}</p>
                                        </div>
                                        <p class="m-0 fw-bold fs-5">{{ $a->receiver_name }}</p>
                                        <p class="m-0">{{ $a->phone_number }}</p>
                                        <p class="m-0">{{ $a->address }}, Kel.{{ $a->kelurahan_desa }}
                                            RT{{ $a->rt }}/RW{{ $a->rw }}, Kel.{{ $a->kelurahan_desa }},
                                            Kec.{{ $a->kecamatan }}, Kab.{{ $a->kota_kabupater }}, {{ $a->provinsi }}
                                            {{ $a->kode_pos }}</p>
                                    </button>
                                </form>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addAddressCartModal" tabindex="-2" aria-labelledby="addAddressCartModal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content color_secondary">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">{{__('cart.addNewAddress')}}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" data-bs-toggle="modal" data-bs-target="#addressCartModal"></button>
                    </div>
                    <form method="POST"
                        action="{{ route('cart.new.address', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name ?? '')]) }}"
                        class="modal-body p-3 px-5">
                        @csrf
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.label')}}</label>
                                <input type="text" name="label_address" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.receiverName')}}</label>
                                <input type="text" name="receiver_name" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.phoneNumber')}}</label>
                                <input type="text" name="receiver_phone" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.address')}}</label>
                                <input type="text" name="address" id="" class="form-control" placeholder=""
                                    required />
                            </div>
                        </div>
                        <div class="col d-flex">
                            <div class="mb-3 pe-2 w-50">
                                <label class="form-label">{{__('cart.rt')}}</label>
                                <input type="text" name="rt" id="" class="form-control" placeholder=""
                                    required />
                            </div>
                            <div class="mb-3 ps-2 w-50">
                                <label class="form-label">{{__('cart.rw')}}</label>
                                <input type="text" name="rw" id="" class="form-control" placeholder=""
                                    required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.subDistrict')}}</label>
                                <input type="text" name="kelurahan" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.district')}}</label>
                                <input type="text" name="kecamatan" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.regency')}}</label>
                                <input type="text" name="kabupaten" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.province')}}</label>
                                <input type="text" name="province" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">{{__('cart.postalCode')}}</label>
                                <input type="text" name="pos_code" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>

                        <button type="submit">{{__('cart.saveAddress')}}</button>
                    </form>
                </div>
            </div>
        </div>
        {{-- End Address Cart Modal --}}
    @endsection
