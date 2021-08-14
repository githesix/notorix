<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-400 active:bg-primary-800 focus:outline-none focus:border-primary-800 focus:ring focus:ring-primary-200 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
