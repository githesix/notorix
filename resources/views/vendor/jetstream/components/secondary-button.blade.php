<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-primary-200 rounded-md font-semibold text-xs text-primary-600 uppercase tracking-widest shadow-sm hover:text-primary-400 focus:outline-none focus:border-acier focus:ring focus:ring-acier active:text-primary-700 active:bg-gray-50 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
