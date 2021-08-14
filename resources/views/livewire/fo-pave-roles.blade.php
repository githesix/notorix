<x-pave title="{{ __('Roles') }}" bg="bg-primary-200" colors="bg-acier text-white border-acier">
    <div class="text-center">
        <p class="text-lg font-bold">{{ __('I am') }}&nbsp;:</p>
        @if ($u->role & 4)
            <p>{{ __('Parent of: :kids', ['kids' => implode(', ', $u->kids->map(function($item, $key){return $item->prenom . ' ' . $item->nom;})->toArray())]) }}</p>
        @endif
        @if ($u->role & 16)
            <p>{{ __('Teaching in') }}
                <button type="button" class="btn btn-acier" wire:click='$emit("openModal", "bo-classe-select-modal", {{ json_encode(["callback" => "syncClasses", "selection" => $checkedclasses]) }})'>
                    {{ count($u->classes) }} {{ __('classes') }}
                </button>
            </p>
        @endif
        @if ($u->role & 32)
            <p>{{ __('Educator in') }}
                <button type="button" class="btn btn-acier" wire:click='$emit("openModal", "bo-classe-select-modal", {{ json_encode(["callback" => "syncClasses", "selection" => $checkedclasses]) }})'>
                    {{ count($u->classes) }} {{ __('classes') }}
                </button>
            </p>
        @endif
        @if ($u->role & 128)
            <p>{{ __('Administrator') }}</p>
        @endif
        @if (count($u->groupes) > 0)
            <p>{!! __('Member of :groups groups', ['groups' => view('partials.implodegroups', ['groupes' => $u->groupes])->render()]) !!}</p>
        @endif
        <p class="mt-2 text-center">
            <button type="button" class="btn btn-bleuis m-1" wire:click='$emit("openModal", "fo-subscribe-as-eleve-modal")'>
                {{ __("I'm student / parent") }}
            </button>
            @if (!($u->role & 48))
                <button type="button" class="btn btn-orangis m-1" wire:click='$emit("openModal", "fo-subscribe-as-teacher-modal")'>
                    {{ __("I'm teacher / educator") }}
                </button>
            @endif
        </p>
    </div>
</x-pave>
