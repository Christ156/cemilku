@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/cart.js') }}" defer></script>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row">
            <form
                action="{{ route('cart.destroy', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name), 'count_items' => $carts->count()]) }}"
                method="POST" class="col-md-8"
                onchange="checkItemSelected({{ $carts->count() }}, {{ json_encode($carts->toArray()) }})">
                @csrf
                @method('DELETE')
                <div class="shipping-address-box-style-2 p-3 mb-3"
                    style="background-color: #FFF8E2; border-radius: 10px; padding: 15px;">
                    <h5 class="fw-bold" style="color: #52282A;">Shipping Address</h5>
                    <div class="d-flex align-items-start mb-2" id="shippingAddressDisplayContainer">
                        <p class="mb-0" id="currentShippingAddress" style="color: #52282A;">
                            {{-- Cumi Cumi Pak Kris Ikan Bakar Sambal Matan, Jalan Pakuan No. 3 Kel. Babakan Madang, Kab. Bogor,
                            Jawa Barat --}}
                            @if ($address_active->count() == 0)
                                <p>Alamat kosong</p>
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
                    @if ($address_active->count() == 0 || $address->count() == 0)
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addressCartModal" class="btn btn-sm"
                            style="background-color: #FFF8E2; border: 1px solid #D1BB9E; color: #52282A; padding: 5px 15px; border-radius: 20px; font-weight: bold;">Tambah
                            alamat baru</button>
                    @else
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addressCartModal" class="btn btn-sm"
                            style="background-color: #FFF8E2; border: 1px solid #D1BB9E; color: #52282A; padding: 5px 15px; border-radius: 20px; font-weight: bold;">Ubah</button>
                    @endif
                </div>

                <div class="select-all d-flex justify-content-between align-items-center">
                    <label class="mb-0">
                        <input type="checkbox" id="select_all"
                            onchange="allCheckboxCheck({{ $carts->count() }}, {{ json_encode($carts->toArray()) }})">
                        Select
                        All
                    </label>

                    <button type="submit" id="remove-btn" class="btn btn-link text-decoration-none" style="display: none">
                        Remove
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
                                @else
                                    @if ($c->customize->type == 'tower')
                                        <img src="{{ asset('assets/tower_selection/preview-tower-layer-4.png') }}"
                                            alt="" class="product-img">
                                    @else
                                        <img src="{{ asset('assets/bouquet_base/base.png') }}" alt=""
                                            class="product-img">
                                    @endif
                                @endif
                                <div class="ms-2">
                                    @if ($c->collection_id != null)
                                        <h6 class="fw-bold mb-0">{{ $c->collection->category }} |
                                            {{ $c->collection->name }}</h6>
                                    @else
                                        <h6 class="fw-bold mb-0">{{ $c->customize->name }} - custom
                                            {{ $c->customize->type }}</h6>
                                    @endif
                                    <small>{{ $c->quantity }} pcs</small>
                                </div>
                            </div>
                            <p class="mb-0 me-3">Rp{{ Str::currency($c->total_price) }}</p>
                        </div>
                    @empty
                        <p>Keranjang anda kosong</p>
                    @endforelse
                </div>


            </form>

            <div class="col-md-4">
                <div class="summary-box p-3">
                    <h5 class="fw-bold" style="color:#52282A;font-size:20px;">
                        Cart (<span id="product-count">0</span> Product)
                    </h5>
                    <hr style="border-top:2px solid #52282A;">

                    <h6 class="mb-1">Shipping</h6>
                    <div class="d-flex justify-content-between">
                        <span>Shipping Regular</span>
                        <span>Rp20.000</span>
                    </div>

                    <hr style="border-top:2px solid #52282A;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold" style="font-size:20px;">Total</h5>
                        <p class="fw-bold mb-0" style="font-size:20px;">Rp<span id="total_price_cart">0</span></p>
                    </div>

                    <hr style="border-top:2px solid #52282A;">
                    <form method="POST"
                        action="{{ route('cart.checkout', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}"
                        class="d-flex justify-content-end">
                        @csrf
                        <input type="hidden" name="total_price" id="total_price" value="0">
                        @forelse ($carts as $c)
                            <input type="hidden" name="item_cart_{{ $c->id }}" id="item_cart_{{ $c->id }}"
                                value="">
                        @empty
                        @endforelse
                        <button id="checkout_btn" type="submit" class="btn btn-primary" disabled>Checkout</button>
                    </form>
                </div>
            </div>
        </div> {{-- Penutup div container --}}

        <!-- Address Cart Modal -->
        <div class="modal fade" id="addressCartModal" tabindex="-1" aria-labelledby="addressCartModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Alamat</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addAddressCartModal"
                            class="w-100 card border border-warning p-2 mb-2">
                            <p class="m-0 fw-bold">Tambah alamat baru</p>
                        </button>
                        <form action="{{route('cart.primary.address',['id_user'=>Auth::user()->id, 'slug'=>Str::slug(Auth::user()->name)])}}" method="post">
                            @method('PUT')
                            @csrf
                            <div class="w-100 card border border-warning p-3 mb-2 bg-warning">
                                <div class="w-100 d-flex justify-content-between">
                                    <p class="fw-bold m-0">{{ $address_active->label }}</p>
                                    <span class="badge bg-light text-dark">Utama</span>
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
                            @forelse ($address as $a)
                                <input type="hidden" name="set-primary-address" value="{{$a->id}}">
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
                            @empty
                            @endforelse
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addAddressCartModal" tabindex="-1" aria-labelledby="addAddressCartModal"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah alamat</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST"
                        action="{{ route('cart.new.address', ['id_user' => Auth::user()->id, 'slug' => Str::slug(Auth::user()->name)]) }}"
                        class="modal-body p-3">
                        @csrf
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Label</label>
                                <input type="text" name="label_address" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Receiver name</label>
                                <input type="text" name="receiver_name" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Phone number</label>
                                <input type="text" name="receiver_phone" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Address</label>
                                <input type="text" name="address" id="" class="form-control" placeholder=""
                                    required />
                            </div>
                        </div>
                        <div class="col d-flex">
                            <div class="mb-3 pe-2 w-50">
                                <label for="" class="form-label">RT</label>
                                <input type="text" name="rt" id="" class="form-control" placeholder=""
                                    required />
                            </div>
                            <div class="mb-3 ps-2 w-50">
                                <label for="" class="form-label">RW</label>
                                <input type="text" name="rw" id="" class="form-control" placeholder=""
                                    required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Kelurahan</label>
                                <input type="text" name="kelurahan" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Kecamatan</label>
                                <input type="text" name="kecamatan" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Kabupaten</label>
                                <input type="text" name="kabupaten" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Provinsi</label>
                                <input type="text" name="province" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="" class="form-label">Pos Code</label>
                                <input type="text" name="pos_code" id="" class="form-control"
                                    placeholder="" required />
                            </div>
                        </div>

                        <button type="submit" data-bs-toggle="modal" data-bs-target="#addressCartModal">Simpan
                            alamat</button>
                    </form>
                </div>
            </div>
        </div>
        {{-- End Address Cart Modal --}}
    @endsection
