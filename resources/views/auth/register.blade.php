<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="w-full justify-center flex">
                <div
                    class="inline-flex rounded border border-primary p-2 mb-4 bg-primary-100 @error('sexe') bg-alert-300 @enderror">
                    <label class="inline-flex items-center m-2">
                        <input class="form-radio" type="radio" name="sexe" value="f" required
                            {{ old('sexe') == 'f' ? 'checked' : '' }}>
                        <span class="ml-2">{{ __('Madame') }}</span>
                    </label>
                    <label class="inline-flex items-center m-2">
                        <input class="form-radio" type="radio" name="sexe" value="m"
                            {{ old('sexe') == 'm' ? 'checked' : '' }}>
                        <span class="ml-2">{{ __('Mister') }}</span>
                    </label>
                    @error('sexe')
                    <p class="text-rougis-500 text-xs italic mt-4">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <x-jet-label for="prenom" value="{{ __('First name') }}" />
                <x-jet-input id="prenom" class="block mt-1 w-full" type="text" name="prenom" :value="old('prenom')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jet-label for="nom" value="{{ __('Last name') }}" />
                <x-jet-input id="nom" class="block mt-1 w-full" type="text" name="nom" :value="old('nom')" required />
            </div>

            <div class="mt-4">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-jet-label for="tel1" value="{{ __('Main phone number') }}" />
                <x-jet-input id="tel1" class="block mt-1 w-full" type="text" name="tel1" :value="old('tel1')" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-jet-label for="terms">
                        <div class="flex items-center">
                            <x-jet-checkbox name="terms" id="terms"/>

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-jet-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-jet-button class="ml-4">
                    {{ __('Register') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
