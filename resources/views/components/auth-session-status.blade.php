@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-white/10 text-white p-4 rounded-md mb-6']) }}>
        {{ $status }}
    </div>
@endif 