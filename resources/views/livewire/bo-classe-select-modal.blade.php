<div>
    <div class="p-4">
        <header class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150 flex">
            <h1 class="text-lg leading-6 font-medium text-gray-900 flex-grow">
                {{ __('Select classes') }}
            </h1>
            <p class="p-2 hover:bg-primary-300 rounded" wire:click="$emit('closeModal')"><x-icons.close /></p>
        </header>
        <div class="flex flex-wrap p-3 m-3 border rounded overflow-y-auto" style="max-height: 65vh;">
            @forelse ($classes as $classe)
            <label class="m-1 p-1 border rounded {{ in_array($classe->id, $selection) ? 'bg-primary-200 text-primary-800' : 'bg-primary-100 text-primary-400'}} hover:bg-primary hover:text-white flex items-center" title="{{ $classe->titulaire }}" wire:key="selcla_{{ $classe->id }}">
                <input type="checkbox" class="form-checkbox" value="{{ $classe->id }}" wire:model="selection">
                <span class="ml-2 flex items-center">{{ $classe->libelle }}</span>
            </label>
            @empty
                <div class="w-full text-center my-4">{{ __('Nothing to show here') }}</div>
            @endforelse
            <div class="w-full mt-6 mb-3 text-center">
                <button type="button" class="btn btn-bleuis" wire:click='select({{ json_encode($selection) }})'>{{ __('Select classes') }}</button>
            </div>
        </div>
    </div>
</div>
