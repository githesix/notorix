<x-app-layout level="blue">
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Kinship with :eleve', ['eleve' => $eleve->prenom . ' ' . $eleve->nom]) }}
        </h2>
    </x-slot>

    <div x-data='eleve' class="text-center">
        <h2 class="font-semibold text-xl leading-tight mt-6 mb-12">
            {{ __('Kinship with :eleve', ['eleve' => $eleve->prenom . ' ' . $eleve->nom]) }}
        </h2>
        @if (!isset($eleve->elu) /* && $user->role == 1 */)
            <div class="my-4">
                <button type="button" class="btn btn-bleuis" x-on:click="confirmeeleve">{{ __('I am :Eleve', ['eleve' => $eleve->prenom . ' ' . $eleve->nom]) }}</button>
                <p class="text-xs mt-0">
                    @if ($simelu > 90)
                        @lang("Of course! Our names are the same.")
                    @elseif ($simelu > 50)
                        @lang('Although our names differ slightly.')
                    @else
                        @lang('Although our names differ completely.')
                    @endif
                </p>
            </div>
        @endif
        @if($user->hasNotRole(2) && !($eleve->parents->contains($user->id)))
            <div class="my-4">
                <button class="btn btn-bleuis" x-on:click="confirmeparent">
                    {{ __('I am parent of :Eleve', ['eleve' => $eleve->prenom . ' ' . $eleve->nom]) }}
                </button>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('eleve', () => ({
                @if (!isset($eleve->elu) /* && $user->role == 1 */)
                    confirmeeleve: function() {
                        Swconfirme.fire({
                            title: "{!! __('Look out!') !!}",
                            text: "{!! __("This operation cannot be canceled except by school staff. Continue?") !!}",
                        }).then((result) => {
                            if (result.value) {
                                this.hestheone();
                            }
                        });
                    },
                    hestheone: function() {
                        axios.post("{{ route('hestheone') }}", {
                            eleveuid: "{{ $eleve->uid }}",
                        })
                        .then(function(response) {
                            if (response.data.error) {
                                console.log(response.data.error);
                                Swal.fire({
                                    icon: "error",
                                    title: "{{ __('Whoops!') }}",
                                    text: response.data.error
                                });
                            } else {
                                window.location.replace("{{ route('dashboard') }}");
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                    },
                @endif
                @if($user->hasNotRole(2) && !($eleve->parents->contains($user->id)))
                    confirmeparent: function() {
                        Swconfirme.fire({
                            title: "{!! __('Look out!') !!}",
                            text: "{!! __("This operation cannot be canceled except by school staff. Continue?") !!}",
                        }).then((result) => {
                            if (result.value) {
                                this.imyourfather();
                            }
                        });
                    },
                    imyourfather: function() {
                        axios.post("{{ route('imyourfather') }}", {
                            eleveuid: '{{ $eleve->uid }}',
                        })
                        .then(function(response) {
                            if (response.data.error) {
                                console.log(response.data.error);
                                Swal.fire({
                                    icon: 'error',
                                    title: "{{ __('Whoops!') }}",
                                    text: response.data.error
                                });
                            } else {
                                window.location.replace("{{ route('dashboard') }}");
                            }
                        })
                        .catch(function(error) {
                            console.log(error);
                        });
                    },
                @endif
            }))
        })
    </script>
    @endpush
</x-app-layout>
