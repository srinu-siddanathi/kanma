@props(['href', 'active' => false])

<a href="{{ $href }}"
   {{ $attributes->merge([
       'class' => ($active ? 'active ' : '') . 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition'
   ]) }}>
    {{ $slot }}
</a> 