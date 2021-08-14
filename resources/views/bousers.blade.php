<x-app-layout level="red" vue="0">
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('User management') }}
        </h2>
    </x-slot>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <livewire:bo-users hideable="select" sort="nom|asc" exportable before-table-slot="partials.bo-users-datatable-header" :groupes="$groupes" :group-filters="$groupFilters" />
    </div>
</div>
</div>
@push('scripts')
@endpush
</x-app-layout>