<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-alert-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-alert-500 focus:outline-none focus:border-alert-700 focus:ring focus:ring-alert-200 active:bg-alert-600 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
