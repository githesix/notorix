@php
    $sex = ['m'=>0,'f'=>1];
    $s = $sex[$u->sexe] ?? 0;
@endphp
<x-app-layout level="blue">
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex flex-wrap">

        @if (auth()->user()->role == 0)
            <x-pave title="{{ __('Welcome :firstname', ['firstname' => $u->prenom])}}" bg="bg-secondary-200" colors="bg-secondary text-white border-secondary">
                <div class="">
                    <p class="text-lg font-bold">{{ __('Status: Invited') }}</p>
                    <p>{{ __('Thank you for accepting our invitation') }}</p>
                    <p>{{ __("We will send you our future notifications to this address: :email", ['email' => $u->email]) }}</p>
                    <p>{{ __('Come back and see us from time to time. This page displays all of the digital services to which you have access.') }}</p>
                </div>
            </x-pave>
        @endif

        @if (auth()->user()->role == 1)
            <livewire:fo-pave-guest />
        @endif

        @if (auth()->user()->role > 3)
            <livewire:fo-pave-roles />
        @endif

        {{-- PLUG-INS «Tuiles» --}}
        @foreach($plugins as $tuile)
        @include($tuile)
        @endforeach

        @xrole(128)
        <x-pave title="{{ __('Administration') }}" bg="bg-alert-200" colors="bg-alert text-white border-alert">
            <div class="text-center">
                <a href="{{ route('BOEleves') }}" class="btn btn-bleuis m-1">
                    {{ App\Models\Eleve::count() }} {{ __('students') }}
                </a>
                <a href="{{ route('BOUsers') }}" class="btn btn-orangis m-1">
                    {{ App\Models\User::count() }} {{ __('users') }}
                </a>
            </div>
        </x-pave>
        @endxrole
    </div>
</x-app-layout>
