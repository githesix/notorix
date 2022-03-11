<input
    type="text"
    autocomplete="off"
    {{ $attributes->class('w-full pl-4 py-2 rounded border border-primary-200 shadow-inner leading-5 text-primary-900 placeholder-primary-400 focus:outline-none focus:border-primary-400 disabled:bg-primary-100') }}
    x-bind:class="[selected ? 'pr-9' : 'pr-4']"
    x-bind:disabled="selected" />
