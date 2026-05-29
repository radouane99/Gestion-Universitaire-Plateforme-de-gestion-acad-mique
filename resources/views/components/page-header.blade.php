@props(['title', 'subtitle' => null, 'icon' => null])

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        @if($icon)
            <div class="w-12 h-12 rounded-2xl bg-white text-upf-blue flex items-center justify-center shadow-sm border border-gray-100">
                {!! $icon !!}
            </div>
        @endif
        <div>
            <h2 class="font-black text-2xl text-upf-blue leading-tight tracking-tighter italic">
                {{ $title }}
            </h2>
            @if($subtitle)
                <p class="text-sm text-gray-500 font-medium mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    @if(isset($actions))
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
