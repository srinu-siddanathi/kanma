@extends('layouts.main')

@section('content')
<section class="py-5 bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4 mb-3">Choose Your Membership Plan</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Membership Plans</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Katha Plans -->
                <div class="mb-5">
                    <h2 class="mb-4">Katha Service Plans</h2>
                    <div class="row">
                        @foreach($kathaPlans as $plan)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 {{ $plan->is_popular ? 'border-primary' : '' }}">
                                @if($plan->is_popular)
                                <div class="card-header bg-primary text-white">
                                    Most Popular
                                </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $plan->name }}</h5>
                                    <p class="card-text text-muted">{{ $plan->description }}</p>
                                    <ul class="list-unstyled mb-4">
                                        <li><i class="bi bi-check-circle-fill text-success"></i> {{ $plan->free_orders }} Free Orders</li>
                                        <li><i class="bi bi-check-circle-fill text-success"></i> Free Delivery within {{ $plan->free_delivery_radius }}KM</li>
                                        @if($plan->wallet_addon > 0)
                                            <li><i class="bi bi-check-circle-fill text-success"></i> ₹{{ number_format($plan->wallet_addon, 2) }} Wallet Addon</li>
                                        @endif
                                    </ul>
                                    <div class="mt-auto">
                                        <div class="text-center mb-3">
                                            <span class="h3 mb-0">₹{{ number_format($plan->price, 2) }}</span>
                                        </div>
                                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">Subscribe Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- O2 Plans -->
                <div>
                    <h2 class="mb-4">O2 Service Plans</h2>
                    <div class="row">
                        @foreach($o2Plans as $plan)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 {{ $plan->is_popular ? 'border-primary' : '' }}">
                                @if($plan->is_popular)
                                <div class="card-header bg-primary text-white">
                                    Most Popular
                                </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $plan->name }}</h5>
                                    <p class="card-text text-muted">{{ $plan->description }}</p>
                                    <ul class="list-unstyled mb-4">
                                        <li><i class="bi bi-check-circle-fill text-success"></i> {{ $plan->free_orders }} Free Orders</li>
                                        <li><i class="bi bi-check-circle-fill text-success"></i> Free Delivery within {{ $plan->free_delivery_radius }}KM</li>
                                        @if($plan->wallet_addon > 0)
                                            <li><i class="bi bi-check-circle-fill text-success"></i> ₹{{ number_format($plan->wallet_addon, 2) }} Wallet Addon</li>
                                        @endif
                                    </ul>
                                    <div class="mt-auto">
                                        <div class="text-center mb-3">
                                            <span class="h3 mb-0">₹{{ number_format($plan->price, 2) }}</span>
                                        </div>
                                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">Subscribe Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 