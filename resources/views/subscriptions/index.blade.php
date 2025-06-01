@extends('layouts.main')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h4 mb-0">My Subscriptions</h2>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>New Subscription
                            </a>
                        </div>

                        @if($subscriptions->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-credit-card display-1 text-muted mb-3"></i>
                                <h3 class="h5 mb-3">No Subscriptions Found</h3>
                                <p class="text-muted mb-4">You haven't subscribed to any plans yet.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    Browse Plans
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Plan</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Payment Method</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscriptions as $subscription)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">{{ $subscription->plan->name }}</h6>
                                                            <small class="text-muted">{{ $subscription->plan->type }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                                                <td>{{ $subscription->ends_at->format('M d, Y') }}</td>
                                                <td>
                                                    @if($subscription->status === 'active')
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif($subscription->status === 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($subscription->status === 'cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @else
                                                        <span class="badge bg-secondary">Expired</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($subscription->payment_method === 'razorpay')
                                                        <span class="text-capitalize">Razorpay</span>
                                                    @else
                                                        <span class="text-capitalize">Wallet</span>
                                                    @endif
                                                </td>
                                                <td>₹{{ number_format($subscription->plan->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $subscriptions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 