{{-- Abandonned because not working --}}
@php
    $table = $this->builder()->getModel()->getTable();
    $column = 'email';
    $rowId = $row->{$this->builder()->getModel()->getTable().'.'.$this->builder()->getModel()->getKeyName()};
    $user = App\Models\User::find($rowId);
    $isVerified = $user->email_verified_at;
@endphp
<div x-data="{
    edit: false,
    edited: false,
    init() {
        window.livewire.on('fieldEdited', (id) => {
            if (id === '{{ $rowId }}') {
                this.edited = true
                setTimeout(() => {
                    this.edited = false
                }, 5000)
            }
        })
    }
}" x-init="init()" :key="{{ $rowId }}">
    <button class="w-full text-left hover:bg-blue-100 px-2 py-1 -mx-2 -my-1 rounded focus:outline-none {{ $isVerified ? 'text-acier' : 'bg-alert-200' }}" x-bind:class="{ 'text-green-600': edited }" x-show="!edit"
        x-on:click="edit = true; $nextTick(() => { $refs.input.focus() })">{!! htmlspecialchars($value) !!}</button>
    <span x-cloak x-show="edit">
        <input class="border-blue-400 px-2 py-1 -mx-2 -my-1 rounded focus:outline-none focus:border" x-ref="input" value="{!! htmlspecialchars($value) !!}"
            wire:change="edited($event.target.value, '{{ $table }}', '{{ $column }}', '{{ $rowId }}')"
            x-on:click.away="edit = false" x-on:blur="edit = false" x-on:keydown.enter="edit = false" />
    </span>
</div>
