<x-guest-layout>
    <div class="h-screen overflow-hidden flex items-center justify-center bg-primary">
        <section class="min-h-screen flex items-stretch text-white ">
            <div class="lg:flex w-1/2 hidden bg-gray-500 bg-no-repeat bg-cover relative items-center" style="background-image: url({{ asset('/perso/img/school_facade.jpg') }});">
                <div class="absolute bg-bleuis-800 opacity-60 inset-0 z-0"></div>
                <div class="w-full px-24 z-10">
                    <h1 class="text-5xl font-bold text-left tracking-wide">NOTORIX</h1>
                    <p class="text-xl my-4">{{ __('User management tool for schools') }}</p>
                    <p class="text-3xl my-4">{{ __('One single account to identify yourself to all :school services', ['school' => config('perso.articleecole').config('perso.lib_ecole')]) }}</p>
                </div>
                <div class="bottom-0 absolute p-4 text-center right-0 left-0 flex justify-center space-x-4">
                    <span class="w-48">
                        <img src="{{asset(config('perso.logo_ecole_s'))}}" alt="{{config('perso.ecole')}}">
                    </span>
                </div>
            </div>
            <div class="lg:w-1/2 w-full flex items-center justify-center text-center md:px-16 px-0 z-0 bg-primary-700 text-primary-100">
                <div class="absolute lg:hidden z-10 inset-0 bg-gray-500 bg-no-repeat bg-cover items-center" style="background-image: url({{ asset('/perso/img/school_facade.jpg') }});">
                    <div class="absolute bg-bleuis-800 opacity-60 inset-0 z-0"></div>
                </div>
                <div class="w-full py-6 z-20">
                    <h1 class="my-6 w-auto h-7 sm:h-8 inline-flex" title="Notorix {{ __('User management tool for schools') }}">
                        <x-jet-authentication-card-logo />
                    </h1>
                    <div class="py-6 space-x-2">
                        <span class="text-4xl text-primary-200">{{ __('Login') }}</span>
                    </div>
                    <p class="text-primary-200">
                        {{ config('perso.lib_ecole') }}
                    </p>

                    <x-jet-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-shamrock">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="sm:w-2/3 w-full px-4 lg:px-0 mx-auto">
                        @csrf
                        <div>
                            {{-- <x-jet-label for="email" value="{{ __('Email') }}" /> --}}
                            <x-jet-input id="email" class="block mt-1 w-full p-4 text-lg rounded-sm bg-primary-800 placeholder-primary-300" type="email" name="email" placeholder="{{ __('Email') }}" :value="old('email')" required autofocus />
                        </div>
                        <div class="mt-4">
                            {{-- <x-jet-label for="password" value="{{ __('Password') }}" /> --}}
                            <x-jet-input id="password" class="block mt-1 w-full p-4 text-lg rounded-sm bg-primary-800 placeholder-primary-300" type="password" name="password" placeholder="{{ __('Password') }}" required autocomplete="current-password" />
                        </div>
                        <div class="block mt-4">
                            <label for="remember_me" class="flex items-center">
                                <x-jet-checkbox id="remember_me" name="remember" />
                                <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
                            </label>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            @if (Route::has('password.request'))
                                <a class="underline text-sm hover:text-secondary" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
            
                            <x-jet-button class="ml-4 bg-acier hover:bg-secondary">
                                {{ __('Log in') }}
                            </x-jet-button>
                        </div>
                        <div class="flex items-center justify-end mt-8">
                            <p><a class="underline hover:text-secondary text-primary-300" href="{{ route('register') }}">{{ __('Not yet registered?') }}</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-guest-layout>
