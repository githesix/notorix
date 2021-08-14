<div>
    <div class="p-4">
        <header class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150 flex">
            <h1 class="text-lg leading-6 font-medium text-gray-900 flex-grow">
                {{ __('Add group') }}
            </h1>
            <p class="p-2 hover:bg-primary-300 rounded" wire:click="$emit('closeModal')"><x-icons.close /></p>
        </header>
        <main>
            <h2 class="text-lg leading-6 font-medium text-primary">{{ __('Add group') }}</h2>
            <div class="m-2 p-2 border bg-platine flex flex-wrap justify-center">
                <div class="w-full">
                    <form wire:submit.prevent="save">
                        <div class="w-full">
                            <x-jet-label for="groupe_nom" value="{{ __('Label') }}" />
                            <x-jet-input id="groupe_nom" type="text" class="mt-1 block w-full" wire:model.lazy="groupe.nom" autofocus />
                            <x-jet-input-error for="groupe.nom" class="mt-2" />
                        </div>
                        <div class="w-full">
                            <x-jet-label for="groupe_description" value="{{ __('Description') }}" />
                            <x-jet-input id="groupe_description" type="text" class="mt-1 block w-full" wire:model.defer="groupe.description" />
                            <x-jet-input-error for="groupe.description" class="mt-2" />
                        </div>
                        <div class="text-right pt-3">
                            <button type="submit" class="btn btn-bleuis mx-3">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>