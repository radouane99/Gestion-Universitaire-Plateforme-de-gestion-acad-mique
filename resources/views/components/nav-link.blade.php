@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-upf-magenta text-[11px] font-black uppercase tracking-widest leading-5 text-upf-blue focus:outline-none transition duration-200 ease-in-out whitespace-nowrap h-20'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-[11px] font-bold uppercase tracking-widest leading-5 text-gray-500 hover:text-upf-blue hover:border-upf-blue/30 focus:outline-none transition duration-200 ease-in-out whitespace-nowrap h-20';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
