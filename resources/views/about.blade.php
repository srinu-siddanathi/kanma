@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">About Us</h1>
                <p class="text-muted">Discover Our Story & Mission</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="p-5">
                    <h2 class="display-5 text-dark mb-4">Who We Are</h2>
                    <p class="lead mb-4">Welcome to FoodMart, your trusted destination for fresh, organic, and high-quality groceries.</p>
                    <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dignissim massa diam elementum habitasse fames semper felis elit. Amet tellus nisl, malesuada volutpat, ac dictum.</p>
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon-box-icon pe-3">
                                    <svg class="quality" width="36" height="36">
                                        <use xlink:href="#quality"></use>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="mb-0">Quality Products</h4>
                                    <p class="mb-0">100% Guaranteed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon-box-icon pe-3">
                                    <svg class="price-tag" width="36" height="36">
                                        <use xlink:href="#price-tag"></use>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="mb-0">Best Prices</h4>
                                    <p class="mb-0">Price Match Guarantee</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img src="{{ asset('images/about-image.jpg') }}" alt="About Us" class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row text-center">
            <div class="col-md-12">
                <h2 class="display-5 text-dark mb-5">Our Values</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 bg-transparent text-center">
                    <div class="card-body">
                        <div class="icon-box-icon mb-4 mx-auto">
                            <svg class="cart-outline" width="48" height="48">
                                <use xlink:href="#cart-outline"></use>
                            </svg>
                        </div>
                        <h3 class="card-title text-dark">Fresh & Organic</h3>
                        <p class="card-text">We source the freshest organic produce directly from local farmers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-transparent text-center">
                    <div class="card-body">
                        <div class="icon-box-icon mb-4 mx-auto">
                            <svg class="shield-security" width="48" height="48">
                                <use xlink:href="#shield-security"></use>
                            </svg>
                        </div>
                        <h3 class="card-title text-dark">Customer First</h3>
                        <p class="card-text">Your satisfaction is our top priority. We ensure quality service every time.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-transparent text-center">
                    <div class="card-body">
                        <div class="icon-box-icon mb-4 mx-auto">
                            <svg class="heart" width="48" height="48">
                                <use xlink:href="#heart"></use>
                            </svg>
                        </div>
                        <h3 class="card-title text-dark">Sustainability</h3>
                        <p class="card-text">We're committed to eco-friendly practices and sustainable packaging.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('components.home.service')
@include('components.home.newsletter')
@endsection 