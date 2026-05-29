<div {{ $attributes->merge(['class' => 'flex items-center space-x-3']) }}>
    <div class="relative">
        <div class="w-12 h-12 bg-upf-blue rounded-xl flex items-center justify-center shadow-lg transform -rotate-3 group-hover:rotate-0 transition-transform">
            <span class="text-white font-black text-2xl tracking-tighter">UPF</span>
        </div>
        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-upf-magenta rounded-lg border-2 border-white shadow-sm"></div>
    </div>
    <div class="hidden xl:block leading-none">
        <p class="text-upf-blue font-black text-lg tracking-tight uppercase">Université Privée de Fès</p>
        <p class="text-upf-magenta text-[10px] font-bold tracking-widest uppercase">Excellence & Innovation</p>
    </div>
</div>
