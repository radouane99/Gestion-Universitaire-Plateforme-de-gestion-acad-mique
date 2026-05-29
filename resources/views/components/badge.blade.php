@props(['type' => 'info'])

@php
    $classes = match($type) {
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'danger', 'error' => 'bg-rose-50 text-rose-700 border-rose-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'primary' => 'bg-upf-blue/10 text-upf-blue border-upf-blue/20',
        'magenta' => 'bg-upf-magenta/10 text-upf-magenta border-upf-magenta/20',
        'neutral' => 'bg-gray-100 text-gray-700 border-gray-200',
        default => 'bg-blue-50 text-blue-700 border-blue-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest border shadow-sm $classes"]) }}>
    {{ $slot }}
</span>
