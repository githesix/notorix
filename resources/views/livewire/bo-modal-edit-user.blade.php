<div>
    <div class="p-4">
        <header class="bg-white p-4 sm:px-6 sm:py-4 border-b border-gray-150 flex">
            <h1 class="text-lg leading-6 font-medium text-gray-900 flex-grow">
                {{ __('Edition of') }} {{ $user->name }}
            </h1>
            <p class="p-2 hover:bg-primary-300 rounded" wire:click="$emit('closeModal')"><x-icons.close /></p>
        </header>
        <x-jet-validation-errors class="mb-4" />

        <form wire:submit.prevent="submit" class="overflow-y-auto" style="max-height: 65vh;">

            <div>
                <div>
                    <x-jet-label for="sexe" value="{{ __('Gender') }}" />
                    <select name="sexe" id="sexe" wire:model="user.sexe">
                        <option value="f">{{ __('Madame') }}</option>
                        <option value="m">{{ __('Mister') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="md:w-1/2 md:pr-2 w-full">
                    <x-jet-label for="prenom" value="{{ __('First name') }}" />
                    <x-jet-input id="prenom" class="block mt-1 w-full" type="text" name="prenom" wire:model="user.prenom" required autocomplete="prenom" />
                </div>
                <div class="md:w-1/2 md:pl-2 md:mt-0 mt-4 w-full">
                    <x-jet-label for="nom" value="{{ __('Last name') }}" />
                    <x-jet-input id="nom" class="block mt-1 w-full" type="text" name="nom" wire:model="user.nom" required />
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="md:w-1/2 md:pr-2 w-full">
                    <x-jet-label for="tel1" value="{{ __('Main phone number') }}" />
                    <x-jet-input id="tel1" class="block mt-1 w-full" type="text" name="tel1" wire:model="user.tel1" />
                </div>
                <div class="md:w-1/2 md:pl-2 md:mt-0 mt-4 w-full">
                    <x-jet-label for="tel2" value="{{ __('Backup phone number') }}" />
                    <x-jet-input id="tel2" class="block mt-1 w-full" type="text" name="tel2" wire:model="user.tel2" />
                </div>
            </div>

            <div class="flex flex-wrap mt-4">
                <div class="md:w-1/2 md:pr-2 w-full">
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" class="block mt-1 w-full {{ $user->email_verified_at ? 'border-verger' : 'border-alert' }}" type="email" name="email" wire:model.lazy="user.username" required />
                </div>
                <div class="md:w-1/2 md:pl-2 md:mt-0 mt-4 w-full self-end flex">
                    @if (isset($user->email_verified_at))
                        <p class="py-2 px-4 bg-verger">{{ __('Confirmed address') }}</p>
                        <button type="button" class="btn btn-rougis" wire:click='$emit("openModal", "modal-confirm", {{ json_encode(["title" => __("Cancel verification"), "body" => __("Attention! :user will have to verify :email address before next login", ["user" => $user->name, "email" => $user->email]), "datas" => ["id" => $user->id, "name" => $user->name, "email" => $user->email], "callback" => "cancelVerification"]) }})'>{{ __('Cancel') }}</button>
                    @else
                        <button class="btn btn-orangis" type="button" wire:click="resendemailverification">{{ __('Resend Verification Email') }}</button>
                        <x-jet-action-message class="mr-3" on="validationlinksent">
                            {{ __('Message sent') }}
                        </x-jet-action-message>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap mt-4">
                <div class="md:w-1/2 md:pr-2 w-full">
                    <x-jet-label for="iban" value="{{ __('IBAN') }}" />
                    <x-jet-input id="iban" class="block mt-1 w-full" type="text" name="iban" wire:model="user.iban" />
                </div>
                <div class="md:w-1/2 md:pl-2 md:mt-0 mt-4 w-full self-end">
                    <button type="button" class="btn btn-rougis" title="{{ __('Reset password') }}" wire:click='$emit("openModal", "modal-confirm", {{ json_encode(["title" => __("Reset password"), "body" => __("Send reset password link to")." ".$user->name." (".$user->username.")", "datas" => ["id" => $user->id, "name" => $user->name, "email" => $user->username], "callback" => "resetPasswordModal"]) }})'>{{ __('Reset password') }}</button>
                </div>
            </div>

            <div class="flex flex-wrap mt-4">
                <h2 class="w-full text-xl pb-2 border-b mb-1">{{ __('Roles') }}</h2>
                @foreach ($roles as $role => $checked)
                <label class="flex items-center m-2 p-1 border rounded">
                    <input type="checkbox" class="form-checkbox" value="{{ $checked }}" wire:model="roles.{{$role}}" wire:click="saveRole">
                    <span class="ml-2">{{ __($bitroles[$role]) }}</span>
                </label>
                @endforeach
            </div>

            @if ($roles[2]) {{-- ##### Eleve-user (elu) ##### --}}
                <div class="mt-4">
                    <h2 class="w-full text-xl pb-2 border-b mb-1">{{ __('Student') }}</h2>
                    <div class="flex flex-wrap p-3">
                        @if (isset($user->elu))
                        @php
                            $elu = App\Models\Eleve::find($user->elu);
                        @endphp
                        <div class="m-1 p-1 bg-primary-200 rounded border">
                            {{ $elu->prenom }} {{ $elu->nom }} ({{ $elu->classe->libelle }})
                            <button type="button" class="p-1 h-6 w-6 text-alert rounded hover:bg-alert hover:text-white" title="{{ __('Delete') }}" wire:click="detachElu({{ $elu->id }})">
                                <svg viewBox="0 0 20 20"  fill="none" stroke="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        @else
                            <button type="button" class="btn btn-orangis" wire:click='$emit("openModal", "bo-eleve-select-modal", {{ json_encode(["callback" => "attachElu"]) }})'>{{ __('Select student') }}</button>
                        @endif
                    </div>
                </div>
            @endif

            @if ($roles[4]) {{-- ##### Parent ##### --}}
                <div class="mt-4">
                    <h2 class="w-full text-xl pb-2 border-b mb-1">{{ __('Kids') }}</h2>
                    <div class="flex flex-wrap p-3">
                        @forelse ($user->kids()->withTrashed()->get() as $kid)
                            <div class="m-1 p-1 {{ $kid->trashed() ? 'bg-secondary-400' : 'bg-primary-200' }} rounded border">
                                {{ $kid->prenom }} {{ $kid->nom }} ({{ $kid->classe->libelle ?? 'XX' }})
                                <button type="button" class="p-1 h-6 w-6 text-alert rounded hover:bg-alert hover:text-white" title="{{ __('Delete') }}" wire:click="detachKid({{ $kid->id }})">
                                    <svg viewBox="0 0 20 20"  fill="none" stroke="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="w-full text-center my-4">{{ __('Nothing to show here') }}</div>
                        @endforelse
                        <button type="button" class="btn btn-orangis" wire:click='$emit("openModal", "bo-eleve-select-modal", {{ json_encode(["callback" => "addKid"]) }})'>{{ __('Add kid') }}</button>
                    </div>
                </div>
            @endif
            @if ($roles[16] || $roles[32]) {{-- ##### Teacher / Educator ##### --}}
                <div class="mt-4">
                    <h2 class="w-full text-xl pb-2 border-b mb-1">{{ __('Classes') }}</h2>
                    <div class="flex flex-wrap p-3">
                        @forelse ($classes as $classe)
                            <div class="m-1 p-1 bg-primary-200 rounded border">
                                {{ $classe->libelle }}
                                <button type="button" class="p-1 h-6 w-6 text-alert rounded hover:bg-alert hover:text-white" title="{{ __('Delete') }}" wire:click="detachClass({{ $classe->id }})">
                                    <svg viewBox="0 0 20 20"  fill="none" stroke="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="w-full text-center my-4">{{ __('Nothing to show here') }}</div>
                        @endforelse
                        <div class="w-full text-center">
                            <button type="button" class="btn btn-orangis" wire:click='$emit("openModal", "bo-classe-select-modal", {{ json_encode(["callback" => "syncClasses", "selection" => $checkedclasses]) }})'>{{ __('Select classes') }}</button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-4">
                <h2 class="w-full text-xl pb-2 border-b mb-1">{{ __('Groups') }} ({{ count($checkedgroups) }})</h2>
                <div>
                    <div class=" max-h-40 overflow-y-auto m-2 p-2 border bg-platine flex flex-wrap justify-center">
                        @forelse ($groupes as $groupe)
                            <label class="m-1 p-1 border rounded {{ in_array($groupe->id, $checkedgroups) ? 'bg-verger' : 'bg-primary-200 hover:bg-primary hover:text-white' }} text-primary-800 flex text-sm">
                                <x-jet-checkbox value="{{ $groupe->id }}" wire:model="checkedgroups" />
                                <p class="ml-1" title="{{ $groupe->description }}">{{ $groupe->nom }}</p>
                            </label>
                        @empty
                            <div class="w-full text-center my-4">{{ __('Nothing to show here') }}</div>
                        @endforelse
                        <button type="button" class="m-1 p-1 border rounded bg-secondary-400 text-primary-800 hover:bg-secondary-600 hover:text-white flex text-sm" wire:click='$emit("openModal", "bo-group-add-modal", {{ json_encode(["userId" => $user->id]) }})'>{{ __('Add group') }}</button>
                    </div>
                </div>
            </div>

            <div class="my-4 flex justify-center">
                <x-jet-button>
                    {{ __('Save') }}
                </x-jet-button>
            </div>
        </form>
    </div>
</div>
