@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
@endsection

@section('script')
    <script src="{{ asset('js/homepage.js') }}"></script>
@endsection

@section('content')
    {{-- BAGIAN CAROUSEL AWAL --}}
    {{-- Carousel Banner --}}
    <section id="banner" class="d-flex justify-content-center align-items-center ps-5 pe-5 "style="padding-top: 120px;">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="3"
                    aria-label="Slide 4"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="4"
                    aria-label="Slide 5"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="5"
                    aria-label="Slide 6"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="6"
                    aria-label="Slide 7"></button>
                <button type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide-to="7"
                    aria-label="Slide 8"></button>
            </div>

            <div class="carousel-inner">

                <div class="carousel-item active">
                    <img src="{{ asset('assets/banner/Banner1.png') }}" class="d-block w-100 img-fluid" alt="First-Slide">
                </div>


                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner2.png') }}" class="d-block w-100 img-fluid" alt="Second Slide">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner3.png') }}" class="d-block w-100 img-fluid" alt="Third Slide">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner4.png') }}" class="d-block w-100 img-fluid" alt="Fourth Slide">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner5.png') }}" class="d-block w-100 img-fluid" alt="Fifth Slide">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner6.png') }}" class="d-block w-100 img-fluid" alt="Sixth Slide">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner7.png') }}" class="d-block w-100 img-fluid" alt="Seventh Slide">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('assets/banner/Banner8.png') }}" class="d-block w-100 img-fluid" alt="Eighth Slide">
                </div>

            </div>
        </div>
    </section>

    {{-- BAGIAN SNACK CUSTOMIZE --}}
    {{-- desktop view --}}
    <div class="container mt-custom d-none d-lg-block">
        <div class="row text-center">
            <a href="{{ route('mysterybox') }}" class="col-lg-4">
                <div class="snack-card" onmouseover="setActive(this)">
                    <img src="{{ asset('assets/banner/bannerSM.png') }}" class="img-fluid" alt="snackMystery">
                    {{-- <button class="btn btn-primary image-button">Customize ></button> --}}
                </div>
            </a>
            <a href="{{ route('customize-tower-bouquet.bouquet') }}" class="col-lg-4">
                <div class="snack-card active" onmouseover="setActive(this)">
                    <img src="{{ asset('assets/banner/bannerSB.png') }}" class="img-fluid" alt="snackBouquet">
                </div>
            </a>

            <a href="{{ route('customize-tower-bouquet.tower') }}" class="col-lg-4">
                <div class="snack-card" onmouseover="setActive(this)">
                    <img src="{{ asset('assets/banner/bannerST.png') }}" class="img-fluid" alt="snackTower">
                </div>
            </a>

        </div>
    </div>

    {{-- mobile view --}}
    <div class="mt-5 d-block d-lg-none position-relative ms-3 me-3">
        <div id = "card-carousel" class="d-flex overflow-auto px-2 scroll-snap-x"
            style="scroll-snap-type: mandatory; scroll-padding:0 50%">

            {{-- card 1 --}}
            <a href="{{ route('mysterybox') }}">
                <div class="card-body flex-shrink-0 me-1"
                    style="width: 67.5vw; max-width: 350px; scroll-snap-align:center">
                    <img src="{{ asset('assets/banner/bannerSM.png') }}" class="img-fluid rounded" alt="snackMystery">
                </div>
            </a>


            {{-- card 2 --}}
            <a href="{{ route('customize-tower-bouquet.bouquet') }}">
                <div class="card-body flex-shrink-0 me-1" id="second-card"
                    style="width: 70vw; max-width: 350px; scroll-snap-align:center">
                    <img src="{{ asset('assets/banner/bannerSB.png') }}" class="img-fluid rounded" alt="snackBouquet">
                </div>
            </a>

            {{-- card 3 --}}
            <a href="{{ route('customize-tower-bouquet.tower') }}">
                <div class="card-body flex-shrink-0 me-1" style="width: 67vw; max-width: 350px; scroll-snap-align:center">
                    <img src="{{ asset('assets/banner/bannerST.png') }}" class="img-fluid rounded" alt="snackTower">
                </div>
            </a>
        </div>

    </div>

    {{-- BAGIAN IMG BULAT BULAT --}}
    <!-- Trending-->
    <div class="mt-5 container-lg">
        <div class="product-card-left d-flex justify-content-center align-items-center">
            <div class="product-image">
                <img src="{{ asset('assets/banner/Group19.png') }}">
            </div>
            <div class="product-info-right top-10 start-50 translate-middle ps-5">
                <p class="product-category">snack tower</p>
                <h3 class="product-title">Anniv Delight</h3>
                <p class="product-price product-price-3">$ 200</p>
            </div>
        </div>

        <div class="product-card-right d-flex justify-content-center align-items-center">
            <div class="product-info-left top-10 end-45 translate-middle ps-5">
                <p class="product-category">snack bouquet</p>
                <h3 class="product-title">Fest Celebration</h3>
                <p class="product-price product-price-2">$ 100</p>
            </div>
            <div class="product-image">
                <img src="{{ asset('assets/banner/Group18bener.png') }}">
            </div>
        </div>

        <div class="product-card-left d-flex justify-content-center align-items-center">
            <div class="product-image">
                <img src="{{ asset('assets/banner/Group19.png') }}">
            </div>
            <div class="product-info-right top-10 start-50 translate-middle ps-5">
                <p class="product-category">snack surprise</p>
                <h3 class="product-title">Happy Combo</h3>
                <p class="product-price product-price-3">$ 75</p>
            </div>
        </div>

        <div class="product-card-right d-flex justify-content-center align-items-center">
            <div class="product-info-left top-10 end-45 translate-middle ps-5">
                <p class="product-category">snack tower</p>
                <h3 class="product-title">Ultimate Combo</h3>
                <p class="product-price product-price-4">$ 175</p>
            </div>
            <div class="product-image">
                <img src="{{ asset('assets/banner/Group20bener.png') }}">
            </div>
        </div>
    </div>


    {{-- BAGIAN ABOUT US BAWAH --}}
    <div class="container my-5 ">
        <div class="row align-items-start">
            <div class="col-12 col-lg-4 mb-4">
                {{-- LOGO --}}
                <img src="{{ asset('assets/logo/cemilku_est.png') }}" alt="cemilku_est"
                    class=" logo-cemilku img-fluid justify-content-center d-block mx-auto">

                {{-- ABOUT US --}}
                {{-- THE BENEFIT --}}
                <div class="title-about-us ps-4 pt-2 pb-2 pe-2 rounded-5 text-white mb-3 mt-3 me-3 bg-blue">
                    <i class="bi bi-star-fill me-2"></i>
                    <span>The Benefit</span>
                </div>

                <ul class="text-justify ps-4 pe-2 ms-4 subtitle-about-us">
                    <li>Customize snacks to your taste</li>
                    <li>Aesthetic designs for gifts or events</li>
                    <li>Flexible with budget</li>
                    <li>Easy and practical to use</li>
                </ul>

            </div>


            <div class="col-12 col-lg-8">
                {{-- WHY CHOOSE US --}}
                <div class="ps-4 pt-2 pb-2 pe-2 rounded-5 text-white mb-3 bg-pink title-about-us text-align-center">
                    <i class="bi bi-star-fill me-2"></i>
                    <span>Why Choose Us?</span>
                </div>

                <p class="text-justify subtitle-about-us ps-4 pe-2">
                    CemilKu! is a modern app that allows you to design your favorite snacks according to your personal
                    taste. We give you the freedom to create aesthetic snack towers, beautiful gift bouquets, or
                    surprise gifts within your budget.
                </p>

                {{-- THE PURPOSE --}}
                <div class="ps-4 pt-2 pb-2 pe-2 rounded-5 text-white mb-3 bg-green title-about-us">
                    <i class="bi bi-star-fill me-2"></i>
                    <span>The Purpose</span>
                </div>

                {{-- BAGIAN KIRI --}}
                <div class="row ps-4 pe-2">
                    <div class="col-4 ps-3 ">
                        <p class="text-justify subtitle-about-us mb-1">Customizable</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-blue probar-custom " role="progressbar" style="width: 95%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <p class="text-justify subtitle-about-us  mb-1 pt-3">Affordable</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-purple probar-custom" role="progressbar" style="width: 82%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>

                        <p class="text-justify  subtitle-about-us  mb-1 pt-3">Flexible</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-green probar-custom" role="progressbar" style="width: 94%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN TENGAH --}}
                    <div class="col-4">
                        <p class="text-justify subtitle-about-us mb-1">Easy</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-green probar-custom" role="progressbar" style="width: 80%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>

                        <p class="text-justify subtitle-about-us mb-1 pt-3">Unique</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-pink probar-custom" role="progressbar" style="width: 90%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>

                        <p class="text-justify subtitle-about-us mb-1 pt-3">Creative</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-purple probar-custom" role="progressbar" style="width: 95%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>

                    </div>

                    {{-- BAGIAN KANAN --}}
                    <div class="col-4">
                        <p class="text-justify subtitle-about-us mb-1">Personal</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-purple probar-custom" role="progressbar" style="width: 70%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>

                        <p class="text-justify subtitle-about-us  mb-1 pt-3">Fun</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-green probar-custom" role="progressbar" style="width: 75%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>

                        <p class="text-justify subtitle-about-us ps-3 mb-1 pt-3">Memorable</p>
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-pink probar-custom" role="progressbar" style="width:60%;"
                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">

                            </div>
                        </div>
                    </div>

                </div>

                {{-- YOUR SNACK, YOUR WAY --}}
                <div class="ps-4 pt-2 pb-2 pe-2 rounded-5 text-white mb-3 mt-4 bg-purple title-about-us">
                    <i class="bi bi-star-fill me-2"></i>
                    <span>Your Snack, Your Way</span>
                </div>

                <p class="text-justify subtitle-about-us ps-4 pe-2">
                    Why bother searching for snacks? CemilKu! offers you the ease of customizing snacks effortlessly,
                    according to your theme and budget!
                </p>


            </div>


        </div>
    </div>

    {{-- FOOTER --}}
    <section class="Footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="{{ asset('assets/logo/logoCemilKu.png') }}" alt="Cemilku Logo">
            </div>

            <div class="footer-info">
                <p><strong>Contact us:</strong></p>
                <p>Email: <a href="mailto:cemilku@gmail.com">cemilku@gmail.com</a></p>
                <p>Phone: 555-567-8901</p>
            </div>

            <div class="footer-social">
                <a href="https://instagram.com/username" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/social_media/ig.png') }}" alt="Instagram">
                </a>
                <a href="https://twitter.com/username" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/social_media/twit.png') }}" alt="Twitter">
                </a>
                <a href="https://facebook.com/username" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/social_media/fb.png') }}" alt="Facebook">
                </a>
            </div>
        </div>

        <hr class="footer-divider">
        <p class="footer-copyright">© 2025 Cemilku. All Rights Reserved.</p>
    </section>
@endsection
