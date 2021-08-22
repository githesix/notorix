<x-pave title="{{ __('Welcome :firstname', ['firstname' => $u->prenom])}}" bg="bg-secondary-200" colors="bg-secondary text-white border-secondary">
    <div class="">
        <p class="text-lg font-bold">{{ __('Status: Invited') }}</p>
        <p>{{ __('A very good start!') }}</p>
        <p class="mt-2 text-center">
            <button type="button" class="btn btn-orangis m-1" wire:click='$emit("openModal", "fo-subscribe-as-eleve-modal")'>
                {{ __("I'm student / parent") }}
            </button>
            <button type="button" class="btn btn-orangis m-1" wire:click='$emit("openModal", "fo-subscribe-as-teacher-modal")'>
                {{ __("I'm teacher / educator") }}
            </button>
        </p>
    </div>
</x-pave>