@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/collections.css') }}">
    <link rel="stylesheet" href="{{asset('css/collection_detail.css')}}">
@endsection

@section('script')
@endsection

@section('content')
    {{-- SEARCH BAR --}}
    <div class="container mt-custom col-12">
        <div class="d-flex align-items-center text-center justify-content-center flex-column flex-md-row ">
            <h1 class="titleSearch d-flex justify-content-center align-items-center">CemilKu!</h1>
            <form class="d-flex" role="search">
                <input class=" search-bar form-control rounded-5" type="search" placeholder="Search Collection..."
                    aria-label="Search" />
                {{-- <button></button> --}}
            </form>
        </div>

        {{-- DIVIDER CNY --}}
        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1" style="border-color: #52282A;">
            <span class="mx-3 fs-3" style="color: #52282A">Chinese New Year Edition</span>
            <hr class="flex-grow-1">
        </div>


        {{-- CARD DESIGN --}}
        <div class="mt-1">
            <div class="row">
                @foreach ($cny as $item)
                    <div class="col-md-3 pb-3 col-6">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('collections.show', $item->id) }}" style="text-decoration: none;">
                                <div class="card text-white bg-light p-1">
                                    <img class="card-img-top p-2" src="{{ asset('assets/collections/'.$item->image) }}" alt="{{ $item->name }}" />
                                    <h4 class="card-title pe-2">{{ $item->name }}</h4>
                                    <p class="card-subtitle">Snack {{ $item->type }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-price">Rp {{ number_format($item->price, 0, ',', '.') }}</h5>
                                        <div>
                                            <button class="btn btn-custom rounded-circle fw-bold fs-4 d-none d-sm-inline"
                                                style="width: 50px; height: 50px;"
                                                onclick="event.stopPropagation();">+</button>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach


                {{-- DIVIDER VALENTINE --}}
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1" style="border-color: #52282A;">
                    <span class="mx-3 fs-3" style="color: #52282A">Valentine Edition</span>
                    <hr class="flex-grow-1">
                </div>


                {{-- VALENTINE --}}
                @foreach ($valentine as $item)
                    <div class="col-md-3 pb-3 col-6">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('collections.show', $item->id) }}" style="text-decoration: none;">
                                <div class="card text-white bg-light p-1">
                                    <img class="card-img-top p-2" src="{{ asset('assets/collections/' . $item->image) }}" alt="Title" />
                                    <h4 class="card-title pe-2">{{ $item->name }}</h4>
                                    <p class="card-subtitle">Snack {{ $item->type }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-price">Rp {{ number_format($item->price, 0, ',', '.') }}</h5>
                                        <div>
                                            <button class="btn btn-custom rounded-circle fw-bold fs-4 d-none d-sm-inline"
                                                style="width: 50px; height: 50px;" onclick="event.stopPropagation();">+</button>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- DIVIDER RAMADHAN --}}
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1" style="border-color: #52282A;">
                    <span class="mx-3 fs-3" style="color: #52282A">Ramadhan Edition</span>
                    <hr class="flex-grow-1">
                </div>


                {{-- RAMADHAN --}}
                @foreach ($ramadhan as $item)
                    <div class="col-md-3 pb-3 col-6">
                        <div class="d-flex justify-content-between">
                            <a href="{{ asset('assets/collections/' . $item->image) }}" style="text-decoration: none;">
                                <div class="card text-white bg-light p-1">
                                    <img class="card-img-top p-2" src="{{ asset('assets/collections/' . $item->image) }}" alt="Title" />
                                    <h4 class="card-title pe-2">{{ $item->name }}</h4>
                                    <p class="card-subtitle">Snack {{ $item->type }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-price">Rp {{ number_format($item->price, 0, ',', '.') }}</h5>
                                        <div>
                                            <button class="btn btn-custom rounded-circle fw-bold fs-4 d-none d-sm-inline"
                                                style="width: 50px; height: 50px;" onclick="event.stopPropagation();">+</button>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach


                {{-- DIVIDER CHRISTMAS --}}
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1" style="border-color: #52282A;">
                    <span class="mx-3 fs-3" style="color: #52282A">Christmas Edition</span>
                    <hr class="flex-grow-1">
                </div>


                {{-- CHRISTMAS --}}
                @foreach ($christmas as $item)
                    <div class="col-md-3 pb-3 col-6">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('collections.show', $item->id) }}" style="text-decoration: none;">
                                <div class="card text-white bg-light p-1">
                                    <img class="card-img-top p-2" src="{{ asset('assets/collections/' . $item->image) }}" alt="Title" />
                                    <h4 class="card-title pe-2">{{ $item->name }}</h4>
                                    <p class="card-subtitle">Snack {{ $item->type }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-price">Rp {{ number_format($item->price, 0, ',', '.') }}</h5>
                                        <div>
                                            <button class="btn btn-custom rounded-circle fw-bold fs-4 d-none d-sm-inline"
                                                style="width: 50px; height: 50px;" onclick="event.stopPropagation();">+</button>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- DIVIDER BIRTHDAY --}}
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1" style="border-color: #52282A;">
                    <span class="mx-3 fs-3" style="color: #52282A">Birthday Edition</span>
                    <hr class="flex-grow-1">
                </div>


                {{-- BIRTHDAY --}}
                @foreach ($birthday as $item)
                    <div class="col-md-3 pb-3 col-6">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('collections.show', $item->id) }}" style="text-decoration: none;">
                                <div class="card text-white bg-light p-1">
                                    <img class="card-img-top p-2" src="{{ asset('assets/collections/' . $item->image) }}" alt="Title" />
                                    <h4 class="card-title pe-2">{{ $item->name }}</h4>
                                    <p class="card-subtitle">Snack {{ $item->type }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-price">Rp {{ number_format($item->price, 0, ',', '.') }}</h5>
                                        <div>
                                            <button class="btn btn-custom rounded-circle fw-bold fs-4 d-none d-sm-inline"
                                                style="width: 50px; height: 50px;" onclick="event.stopPropagation();">+</button>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach


                {{-- DIVIDER GRADUATION --}}
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1" style="border-color: #52282A;">
                    <span class="mx-3 fs-3" style="color: #52282A">Graduation Edition</span>
                    <hr class="flex-grow-1">
                </div>


                {{-- GRADUATION --}}
                @foreach ($graduation as $item)
                    <div class="col-md-3 pb-3 col-6">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('collections.show', $item->id) }}" style="text-decoration: none;">
                                <div class="card text-white bg-light p-1">
                                    <img class="card-img-top p-2" src="{{ asset('assets/collections/' . $item->image) }}" alt="Title" />
                                    <h4 class="card-title pe-2">{{ $item->name }}</h4>
                                    <p class="card-subtitle">Snack {{ $item->type }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-price">Rp {{ number_format($item->price, 0, ',', '.') }}</h5>
                                        <div>
                                            <button class="btn btn-custom rounded-circle fw-bold fs-4 d-none d-sm-inline"
                                                style="width: 50px; height: 50px;" onclick="event.stopPropagation();">+</button>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
