<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 hover:text-upf-blue focus:outline-none focus:ring-2 focus:ring-upf-blue focus:ring-offset-2 transition ease-in-out duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5']) }}>
    {{ $slot }}
</button>
