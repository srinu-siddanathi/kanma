@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-dark">About Us</h1>
                <p class="text-muted">Live Like King – That's KANMA</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="p-5">
                    <h2 class="display-5 text-dark mb-4">Who We Are</h2>
                    <p class="lead mb-4">At KANMA, we believe convenience is not a luxury — it's a right.</p>
                    <p class="mb-4">We're building a revolutionary quick-commerce and household service platform that empowers you to live a truly stress-free life. Whether it's ordering groceries, fixing a broken tap, booking a maid, or requesting laundry services, KANMA brings everything to your doorstep — fast, easy, and reliable.</p>
                    <p class="mb-4">But we don't stop there. With our unique "Own Order" feature, you're no longer limited to a catalog. Just call or chat in the app to request anything you need, and we'll handle the rest.</p>
                    <p class="mb-4">Built for the modern Indian lifestyle, KANMA operates on a freemium subscription model where quality, speed, and safety are guaranteed. We prioritize user privacy, ensure secure transactions with Razorpay, and enforce strict no-delivery policies for alcohol or inappropriate items.</p>
                    <p class="mb-4"><strong>KANMA isn't just an app — it's your personal butler for everyday life.</strong></p>
                    <p class="mb-4"><em>Live easy. Live like a king.</em></p>
                    
                </div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M21.5 15a3 3 0 0 0-1.9-2.78l1.87-7a1 1 0 0 0-.18-.87A1 1 0 0 0 20.5 4H6.8l-.33-1.26A1 1 0 0 0 5.5 2h-2v2h1.23l2.48 9.26a1 1 0 0 0 1 .74H18.5a1 1 0 0 1 0 2h-13a1 1 0 0 0 0 2h1.18a3 3 0 1 0 5.64 0h2.36a3 3 0 1 0 5.82 1a2.94 2.94 0 0 0-.4-1.47A3 3 0 0 0 21.5 15Zm-3.91-3H9L7.34 6H19.2ZM9.5 20a1 1 0 1 1 1-1a1 1 0 0 1-1 1Zm8 0a1 1 0 1 1 1-1a1 1 0 0 1-1 1Z"/>
                            </svg>
                        </div>
                        <h3 class="card-title text-dark">Convenience First</h3>
                        <p class="card-text">We bring everything to your doorstep — fast, easy, and reliable.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 bg-transparent text-center">
                    <div class="card-body">
                        <div class="icon-box-icon mb-4 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M19.63 3.65a1 1 0 0 0-.84-.2a8 8 0 0 1-6.22-1.27a1 1 0 0 0-1.14 0a8 8 0 0 1-6.22 1.27a1 1 0 0 0-.84.2a1 1 0 0 0-.37.78v7.45a9 9 0 0 0 3.77 7.33l3.65 2.6a1 1 0 0 0 1.16 0l3.65-2.6A9 9 0 0 0 20 11.88V4.43a1 1 0 0 0-.37-.78ZM18 11.88a7 7 0 0 1-2.93 5.7L12 19.77l-3.07-2.19A7 7 0 0 1 6 11.88v-6.3a10 10 0 0 0 6-1.39a10 10 0 0 0 6 1.39Zm-4.46-2.29l-2.69 2.7l-.89-.9a1 1 0 0 0-1.42 1.42l1.6 1.6a1 1 0 0 0 1.42 0L15 11a1 1 0 0 0-1.42-1.42Z"/>
                            </svg>
                        </div>
                        <h3 class="card-title text-dark">Privacy & Security</h3>
                        <p class="card-text">We prioritize user privacy and ensure secure transactions with Razorpay.</p>
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
                        <h3 class="card-title text-dark">Personal Butler</h3>
                        <p class="card-text">Your personal butler for everyday life with our unique "Own Order" feature.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('components.home.service')
@endsection 