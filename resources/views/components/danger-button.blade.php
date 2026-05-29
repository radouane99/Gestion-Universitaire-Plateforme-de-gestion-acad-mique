<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-rose-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-300 shadow-lg hover:shadow-rose-200 transform hover:-translate-y-0.5']) }}>
    {{ $slot }}
</button>
