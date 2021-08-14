<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Notorix') }}</title>

        <meta name="author" content="Thesis asbl">
        <meta name="description" content="{{ _("User management tool for schools") }}">
        <meta name="keywords" content="thesix,repertoire,data mining,rgpd,gdpr">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @livewireStyles

        <!-- Scripts -->
        {{-- <script src="{{ mix('js/manifest.js') }}" defer></script> --}}
        {{-- <script src="{{ mix('js/vendor-vue.js') }}" defer></script>
        <script src="{{ mix('js/vendor-moment.js') }}" defer></script> --}}
        {{-- <script src="{{ mix('js/vendor.js') }}" defer></script> --}}
        @if ($vue ?? '')
        @livewireScripts
        <script src="{{ mix('js/app-vue.js') }}"></script>
        @else
        <script src="{{ mix('js/app.js') }}" defer></script>
        @endif
    </head>
    <body class="font-sans antialiased bg-platine text-bleuis">
        <x-jet-banner />
        <div class="flex flex-col min-h-screen bg-gray-100">
            @livewire('navigation-menu')
            <x-statusalert />
            <!-- Page Heading -->
            @if (isset($header))
            <header class="{{ $level['bg'] }} {{ $level['text'] }} shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endif
            <!-- Page Content -->
            <main class="flex-auto" id="app">
                {{ $slot }}
            </main>
            <!-- Page Footer -->
            <footer class="text-center bg-bleuis text-sm text-orangis-300 py-1 mt-2 font-bold h-20 md:h-auto">
                Notorix<br>
                {!! __("Made with :love by :thesis :date", ['date' => date('Y'), 'thesis' => '<a href="https://the6.be">Thesis&#169;</a>', 'love' => '<svg class="fill-current pointer-events-none text-alert inline w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3.22l-.61-.6a5.5 5.5 0 0 0-7.78 7.77L10 18.78l8.39-8.4a5.5 5.5 0 0 0-7.78-7.77l-.61.61z" /></svg>']) !!}
            </footer>
        </div>
        @stack('vuescripts')
        @stack('modals')
        @livewire('livewire-ui-modal')
        @if (!$vue)
        @livewireScripts
        @endif
        <script>
            window.livewire.onError(statusCode => {
                if (statusCode === 419) {
                    if (confirm("{{ __('This page has expired due to inactivity. Would you like to refresh the page?') }}")) {
                        document.location.reload();
                    }
            
                    return false
                }
            })
        </script>
        @if(config('perso.tawk_show'))
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
            @auth
            Tawk_API.visitor = {
                name  : "{{ Auth::user()->name }}",
                email : "{{ Auth::user()->email }}"
            };
            Tawk_API.onLoad = function(){
                Tawk_API.setAttributes({
                    'role'    : "{{ Auth::user()->rolevue }}"
                }, function(error){});
            };
            @endauth
            (function(){
                var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                s1.async=true;
                s1.src="{{ config('perso.tawk_src') }}";
                s1.charset='UTF-8';
                s1.setAttribute('crossorigin','*');
                s0.parentNode.insertBefore(s1,s0);
            })();
        </script>
        <!--End of Tawk.to Script-->
        @endif
        @stack('scripts')
    </body>
</html>
