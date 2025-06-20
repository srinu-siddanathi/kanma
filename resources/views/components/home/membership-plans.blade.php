@php
    $o2Plans = \App\Models\SubscriptionPlan::where('type', 'o2')
        ->where('is_active', true)
        ->orderBy('price')
        ->get();
    
    $kathaPlans = \App\Models\SubscriptionPlan::where('type', 'katha')
        ->where('is_active', true)
        ->orderBy('price')
        ->get();

    $activeSubscription = auth()->check() ? auth()->user()->subscriptions()
        ->where('status', 'active')
        ->where('ends_at', '>', now())
        ->with('plan')
        ->first() : null;
@endphp

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold mb-3">Choose Your Membership Plan</h2>
            <p class="lead text-muted">Select the perfect plan for your needs</p>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-pills nav-justified mb-4" id="planTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="katha-tab" data-bs-toggle="pill" data-bs-target="#katha" type="button" role="tab" aria-controls="katha" aria-selected="true">
                    <i class="bi bi-book me-2"></i>Kanma Services
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="o2-tab" data-bs-toggle="pill" data-bs-target="#o2" type="button" role="tab" aria-controls="o2" aria-selected="false">
                    <i class="bi bi-truck me-2"></i>O2 Service
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="planTabsContent">
            <!-- Katha Plans -->
            <div class="tab-pane fade show active" id="katha" role="tabpanel" aria-labelledby="katha-tab">
                @if($kathaPlans->isEmpty())
                    <div class="alert alert-info text-center">
                        No active Katha service plans available at the moment.
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($kathaPlans as $plan)
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm d-flex flex-column {{ $activeSubscription && $activeSubscription->plan->id === $plan->id ? 'border-primary' : '' }}">
                                    @if($activeSubscription && $activeSubscription->plan->id === $plan->id)
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge bg-primary">Current Plan</span>
                                        </div>
                                    @endif
                                    <div class="card-body p-4 d-flex flex-column">
                                        <h3 class="h5 mb-3">{{ $plan->name }}</h3>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="h3 mb-0">₹{{ number_format($plan->price, 2) }}</span>
                                            <span class="text-muted">/ {{ $plan->validity_days }} days</span>
                                        </div>
                                        <p class="text-muted mb-4">{{ $plan->description }}</p>
                                        <ul class="list-unstyled mb-4">
                                            @if($plan->wallet_addon > 0)
                                                <li class="mb-2">
                                                    <i class="bi bi-wallet2 text-success me-2"></i>
                                                    Wallet Addon: {{ $plan->wallet_addon }}
                                                </li>
                                            @endif
                                            @if($plan->free_orders > 0)
                                                <li class="mb-2">
                                                    <i class="bi bi-cart-check text-success me-2"></i>
                                                    {{ $plan->free_orders }} Free Special Orders
                                                </li>
                                            @endif
                                            @if($plan->free_delivery_radius > 0)
                                                <li class="mb-2">
                                                    <i class="bi bi-geo-alt text-success me-2"></i>
                                                    Free Delivery within {{ $plan->free_delivery_radius }} KM
                                                </li>
                                            @endif
                                        </ul>
                                        <div class="mt-auto">
                                            @auth
                                                @if($activeSubscription)
                                                    @if($activeSubscription->plan->id === $plan->id)
                                                        <button class="btn btn-secondary w-100" disabled>
                                                            Current Plan
                                                        </button>
                                                    @elseif($activeSubscription->plan->type === $plan->type)
                                                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">
                                                            Upgrade Plan
                                                        </a>
                                                    @else
                                                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">
                                                            Subscribe Now
                                                        </a>
                                                    @endif
                                                @else
                                                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">
                                                        Subscribe Now
                                                    </a>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                    Login to Subscribe
                                                </button>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- O2 Plans -->
            <div class="tab-pane fade" id="o2" role="tabpanel" aria-labelledby="o2-tab">
                @if($o2Plans->isEmpty())
                    <div class="alert alert-info text-center">
                        No active O2 service plans available at the moment.
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($o2Plans as $plan)
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm d-flex flex-column {{ $activeSubscription && $activeSubscription->plan->id === $plan->id ? 'border-primary' : '' }}">
                                    @if($activeSubscription && $activeSubscription->plan->id === $plan->id)
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge bg-primary">Current Plan</span>
                                        </div>
                                    @endif
                                    <div class="card-body p-4 d-flex flex-column">
                                        <h3 class="h5 mb-3">{{ $plan->name }}</h3>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="h3 mb-0">₹{{ number_format($plan->price, 2) }}</span>
                                            <span class="text-muted">/ {{ $plan->validity_days }} days</span>
                                        </div>
                                        <p class="text-muted mb-4">{{ $plan->description }}</p>
                                        <ul class="list-unstyled mb-4">
                                            @if($plan->wallet_addon > 0)
                                                <li class="mb-2">
                                                    <i class="bi bi-wallet2 text-success me-2"></i>
                                                    Wallet Addon: {{ $plan->wallet_addon }}
                                                </li>
                                            @endif
                                            @if($plan->free_orders > 0)
                                                <li class="mb-2">
                                                    <i class="bi bi-cart-check text-success me-2"></i>
                                                    {{ $plan->free_orders }} Free Orders
                                                </li>
                                            @endif
                                            @if($plan->free_delivery_radius > 0)
                                                <li class="mb-2">
                                                    <i class="bi bi-geo-alt text-success me-2"></i>
                                                    Free Delivery within {{ $plan->free_delivery_radius }} KM
                                                </li>
                                            @endif
                                        </ul>
                                        <div class="mt-auto">
                                            @auth
                                                @if($activeSubscription)
                                                    @if($activeSubscription->plan->id === $plan->id)
                                                        <button class="btn btn-secondary w-100" disabled>
                                                            Current Plan
                                                        </button>
                                                    @elseif($activeSubscription->plan->type === $plan->type)
                                                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">
                                                            Upgrade Plan
                                                        </a>
                                                    @else
                                                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">
                                                            Subscribe Now
                                                        </a>
                                                    @endif
                                                @else
                                                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">
                                                        Subscribe Now
                                                    </a>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                    Login to Subscribe
                                                </button>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section> 