<div class="p-4">
    <header class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150 flex">
        <h1 class="text-lg leading-6 font-medium text-gray-900 flex-grow">
            {{ __("Teacher's access code") }}
        </h1>
        <p class="p-2 hover:bg-primary-300 rounded" wire:click="$emit('closeModal')"><x-icons.close /></p>
    </header>
    <div class="bg-fantomis border-2 rounded shadow-md animate-zoom"
    x-data="{ code: '',
        balance: function() {
            window.location.assign('{{ route('prof') }}/' + this.code);
        }
    }"
    >
        <div class="m-8 p-8 text-center border rounded border-bleuis-200">
            <input type="text" class="rounded mb-4 bg-bleuis-200 w-full" ref="code" x-model="code"
                x-on:keyup.enter="balance" autofocus>
            <p class="mb-4">{{ __('Input or scan the code delivered by school') }}
            </p>
            <button class="btn btn-bleuis mx-2" x-on:click="balance">{{ __('Confirm') }}</button>
        </div>
    </div>
</div>
