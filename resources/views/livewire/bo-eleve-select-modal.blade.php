<div>
    <div class="p-4">
        <header class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150 flex">
            <h1 class="text-lg leading-6 font-medium text-gray-900 flex-grow">
                {{ __('Select student') }}
            </h1>
            <p class="p-2 hover:bg-primary-300 rounded" wire:click="$emit('closeModal')"><x-icons.close /></p>
        </header>
        <div>
            <input type="text" class="w-full border-primary" wire:model="searcheleves" placeholder="{{ __('Search among the students') }}" wire:keydown.debounce="search" autofocus />
            <div class="flex flex-wrap p-3 m-3 border rounded overflow-y-auto" style="max-height: 65vh;">
                @forelse ($foundeleves as $eleve)
                    <button type="button" wire:click="select({{ $eleve->id }})" class="p-1 m-1 bg-verger rounded">
                        {{ $eleve->prenom }} {{ $eleve->nom }} ({{ $eleve->classe->libelle }})
                    </button>
                @empty
                <div class="w-full text-center my-4">{{ __('Nothing to show here') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
