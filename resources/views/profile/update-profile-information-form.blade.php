<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            wire:model="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-jet-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview">
                    <span class="block rounded-full w-20 h-20"
                          x-bind:style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-jet-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-jet-secondary-button>
                @endif

                <x-jet-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Gender -->
        <div class="col-span-6 sm:col-span-4">
            <label class="block">
                {{ __('Gender') }}
                <select class="form-select block w-full mt-1" id="sexe" wire:model.defer="state.sexe">
                    <option value="f">{{ __('Madame') }}</option>
                    <option value="m">{{ __('Mister') }}</option>
                </select>
            </label>
        </div>

        <!-- Prénom -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="prenom" value="{{ __('First name') }}" />
            <x-jet-input id="prenom" type="text" class="mt-1 block w-full" wire:model.defer="state.prenom" autocomplete="prenom" />
            <x-jet-input-error for="prenom" class="mt-2" />
        </div>

        <!-- Nom -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="nom" value="{{ __('Last name') }}" />
            <x-jet-input id="nom" type="text" class="mt-1 block w-full" wire:model.defer="state.nom" autocomplete="nom" />
            <x-jet-input-error for="nom" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="email" value="{{ __('Email') }}" />
            <x-jet-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="state.username" />
            <x-jet-input-error for="email" class="mt-2" />
        </div>

        <!-- TEL1 -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="tel1" value="{{ __('Main phone number') }}" />
            <x-jet-input id="tel1" type="text" class="mt-1 block w-full" wire:model.defer="state.tel1" autocomplete="tel1" />
            <x-jet-input-error for="tel1" class="mt-2" />
        </div>

        <!-- TEL2 -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="tel2" value="{{ __('Backup phone number') }}" />
            <x-jet-input id="tel2" type="text" class="mt-1 block w-full" wire:model.defer="state.tel2" autocomplete="tel2" />
            <x-jet-input-error for="tel2" class="mt-2" />
        </div>

        <!-- IBAN -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="iban" value="{{ __('IBAN') }}" />
            <x-jet-input id="iban" type="text" class="mt-1 block w-full" wire:model.defer="state.iban" autocomplete="iban" />
            <x-jet-input-error for="iban" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
