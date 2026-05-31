@php
    $classes = 'inline-flex items-center px-6 py-3 bg-upf-blue border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-upf-navy focus:bg-upf-navy active:bg-upf-navy focus:outline-none focus:ring-2 focus:ring-upf-magenta focus:ring-offset-2 transition ease-in-out duration-300 shadow-lg hover:shadow-blue-200 transform hover:-translate-y-0.5';
@endphp

@if ($attributes->has('href') || $attributes->get('tag') == 'a')
    <a {{ $attributes->except('tag')->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
