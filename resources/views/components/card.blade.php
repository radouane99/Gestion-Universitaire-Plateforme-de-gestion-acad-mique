<div {{ $attributes->merge(['class' => 'bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden']) }}>
    @if(isset($header))
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
            {{ $footer }}
        </div>
    @endif
</div>
