<div class="flex flex-wrap">
    @if ($trashed)
    <p class="p-1 bg-alert text-white rounded m-1">{{ __('Deleted') }}</p>
    @endif
    @if (!($r & 1))
        <p class="p-1 bg-verger rounded m-1 cursor-pointer" wire:click="activate({{ $id }})">{{ __('Activate') }}</p>
    @endif
    @foreach ($roles as $role)
    @if ($role !== 'Active')
        <p class="p-1 bg-primary-200 rounded m-1">{{ __($role) }}</p>
    @endif
    @endforeach
</div>