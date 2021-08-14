<div wire:key="rpw_{{ $id }}">
    <span wire:click="$set('confirmingResetPw', true)">
        <button class="p-1 text-primary rounded hover:bg-primary hover:text-white"><x-icons.resetpw /></button>
    </span>

    <x-jet-confirmation-modal wire:model="confirmingResetPw">
        <x-slot name="title">
            Reset password
        </x-slot>
    
        <x-slot name="content">
            <p>{{ __('Send reset password link to') }} {{ $name }} ({{ $email }})</p>
            <p>{{ __('This link will expire in :expirein minutes', ['expirein' => config('auth.passwords.users.expire')])}}</p>
        </x-slot>
    
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingResetPw')" wire:loading.attr="disabled">
                No
            </x-jet-secondary-button>
    
            <x-jet-danger-button class="ml-2" wire:click="resetPassword('{{ $email }}')" wire:loading.attr="disabled">
                Send reset password link
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
