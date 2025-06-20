@extends('layouts.main')

@section('content')

@if(isset($error))
<div class="alert alert-danger">
    {{ $error }}
</div>
@elseif(isset($data))
@include('components.home.banner-slider', ['banners' => $data->banners])
@include('components.home.category-slider', ['categories' => $data->categories])

@include('components.home.shops')

@if($data->trending_products && $data->trending_products->count())
    @include('components.home.trend-products', ['products' => $data->trending_products])
@endif

@include('components.home.membership-plans')

@include('components.home.newsletter')
@include('components.home.shop-owner-registration')
@include('components.home.best-selling', ['products' => $data->best_selling])
@include('components.home.just-arrived', ['products' => $data->just_arrived])
{{-- @include('components.home.blog', ['posts' => $data->blog_posts]) --}}
@include('components.home.app-ad')
@include('components.home.looking')
@include('components.home.service')
@endif

@endsection

@push('scripts')
<script>
// Initialize countdown timer if available
// @if(isset($data) && $data->has_countdown > 0)
// const dealEndsAt = new Date('{{ $data->deal_countdown["endsat"] }}').getTime();

// function updateCountdown() {
//     const now = new Date().getTime();
//     const distance = dealEndsAt - now;

//     if (distance > 0) {
//         document.getElementById('countdown-days').textContent = Math.floor(distance / (1000 * 60 * 60 * 24));
//         document.getElementById('countdown-hours').textContent = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
//         document.getElementById('countdown-minutes').textContent = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
//         document.getElementById('countdown-seconds').textContent = Math.floor((distance % (1000 * 60)) / 1000);
//     }
// }

// setInterval(updateCountdown, 1000);
// updateCountdown();
@endif
</script>
@endpush