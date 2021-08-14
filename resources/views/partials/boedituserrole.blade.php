<div class="flex flex-wrap">
    @if ($trashed)
    <p class="p-1 bg-alert text-white rounded m-1">{{ __('Deleted') }}</p>
    @endif
    @foreach ($roles as $role)
        <p class="p-1 bg-primary-200 rounded m-1">{{ __($role) }}</p>
    @endforeach
</div>