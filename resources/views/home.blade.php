@extends('layouts.main')

@section('content')

@include('components.home.banner-slider')
@include('components.home.category-slider')
@include('components.home.brands-slider')
@include('components.home.trend-products')
@include('components.home.newsletter')
@include('components.home.best-selling')
@include('components.home.just-arrived')
@include('components.home.blog')
@include('components.home.app-ad')
@include('components.home.looking')
@include('components.home.service')
@endsection
@push('scripts')
<script>
// Any page-specific scripts can go here
</script>
@endpush