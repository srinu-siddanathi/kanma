@extends('layouts.main')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">Complete Your Subscription</h2>

                        <div class="plan-details mb-4 p-4 bg-light rounded">
                            <h3 class="h5 mb-3">{{ $plan->name }}</h3>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Price:</span>
                                <span class="h4 mb-0">₹{{ number_format($plan->price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Validity:</span>
                                <span>{{ $plan->validity_days }} days</span>
                            </div>
                            @if($plan->wallet_addon > 0)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Wallet Addon:</span>
                                    <span class="text-success">+₹{{ number_format($plan->wallet_addon, 2) }}</span>
                                </div>
                            @endif
                            @if($plan->free_orders > 0)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Free Orders:</span>
                                    <span>{{ $plan->free_orders }}</span>
                                </div>
                            @endif
                            @if($plan->free_delivery_radius > 0)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Free Delivery Radius:</span>
                                    <span>{{ $plan->free_delivery_radius }} KM</span>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('subscription.process', $plan) }}" method="POST" id="payment-form">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label">Select Payment Method</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay" checked>
                                        <label class="form-check-label" for="razorpay">
                                            Razorpay
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="wallet" value="wallet">
                                        <label class="form-check-label" for="wallet">
                                            Wallet Balance (₹{{ number_format(auth()->user()->wallet_balance, 2) }})
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Pay ₹{{ number_format($plan->price, 2) }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('payment-form');
    const walletRadio = document.getElementById('wallet');
    const razorpayRadio = document.getElementById('razorpay');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (walletRadio.checked) {
            const walletBalance = {{ auth()->user()->wallet_balance }};
            const planPrice = {{ $plan->price }};

            if (walletBalance < planPrice) {
                alert('Insufficient wallet balance. Please choose another payment method.');
                return;
            }
            form.submit();
        } else if (razorpayRadio.checked) {
            try {
                const response = await fetch('{{ route("subscription.process", $plan) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_method: 'razorpay'
                    })
                });

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                const options = {
                    key: '{{ config("services.razorpay.key") }}',
                    amount: data.amount,
                    currency: data.currency,
                    name: '{{ config("app.name") }}',
                    description: '{{ $plan->name }} Subscription',
                    order_id: data.id,
                    handler: async function (response) {
                        try {
                            const verifyResponse = await fetch('{{ route("subscription.verify-payment") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                    subscription_id: data.subscription_id
                                })
                            });

                            const verifyData = await verifyResponse.json();

                            if (verifyData.error) {
                                throw new Error(verifyData.error);
                            }

                            window.location.href = '{{ route("home") }}';
                        } catch (error) {
                            alert('Payment verification failed: ' + error.message);
                        }
                    },
                    prefill: {
                        name: '{{ auth()->user()->name }}',
                        email: '{{ auth()->user()->email }}',
                        contact: '{{ auth()->user()->phone }}'
                    },
                    theme: {
                        color: '#0d6efd'
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
            } catch (error) {
                alert('Failed to initialize payment: ' + error.message);
            }
        }
    });
});
</script>
@endpush 