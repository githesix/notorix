<div>
    <x-jet-action-message class="mr-3" on="validationlinksent">
        {{ __('Message sent') }}
    </x-jet-action-message>
    <div class="flex flex-wrap mx-4">
        <h2 class="w-full text-xl pb-2 border-b mb-1">{{ __('Filters') }}</h2>
        <label class="flex items-center m-2 p-1 border rounded">
            <input type="checkbox" class="form-checkbox" value="{{ $withTrashed }}" wire:model="withTrashed" wire:click="init">
            <span class="ml-2">{{ __('Deleted') }}</span>
        </label>
        @foreach ($filters as $filter => $checked)
        <label class="flex items-center m-2 p-1 border rounded">
            <input type="checkbox" class="form-checkbox" value="{{ $checked[0] }}" wire:model="filters.{{$filter}}.0" wire:click="init">
            <span class="ml-2">{{ __($filter) }}</span>
        </label>
        @endforeach
        <div class="w-full" x-data="{collapse:true}">
            <button type="button" x-on:click="collapse = !collapse">
                <h2 class="w-full text-xl py-2 border-b mb-1 flex items-center">{{ __('Groups') }} <span class="ml-16"><x-icons.chevron-up x-show="!collapse" x-cloak /><x-icons.chevron-down x-show="collapse" /></span></h2>
            </button>
            <div x-cloak class="flex flex-wrap text-xs transition-all duration-700" x-show="!collapse" x-transition>
                @forelse ($groupes as $groupe)
                @php
                    $usercount = $groupe->users_count ?? count($groupe->users);
                @endphp
                    <label class="m-1 p-1 border rounded {{ $usercount > 0 ? 'bg-primary-200 text-primary-800' : 'bg-primary-100 text-primary-400'}} hover:bg-primary hover:text-white flex items-center" title="{{ $groupe->description }}" wire:key="grfil_{{ $groupe->id }}">
                        <input type="checkbox" class="form-checkbox" value="{{ $groupFilters[$groupe->id] }}" wire:model="groupFilters.{{$groupe->id}}" wire:click="init">
                        <span class="ml-2 flex items-center">{{ $groupe->nom }}
                            {{-- Count usage or trash button --}}
                            @if ($usercount > 0)
                                ({{ $usercount }})
                            @else
                                <button class="p-1 h-6 w-6 text-alert rounded hover:bg-alert hover:text-white" title="{{ __('Delete') }}" wire:click="deleteGroupe({{ $groupe->id }})" type="button">
                                    <svg viewBox="0 0 20 20"  fill="none" stroke="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endif
                        </span>
                    </label>
                @empty
                    <div class="w-full text-center my-4">{{ __('Nothing to show here') }}</div>
                @endforelse
                <button type="button" class="m-1 p-1 border rounded bg-secondary-400 text-primary-800 hover:bg-secondary-600 hover:text-white flex text-sm" wire:click='$emit("openModal", "bo-group-add-modal")'>{{ __('Add group') }}</button>
            </div>
        </div>
    </div>
</div>