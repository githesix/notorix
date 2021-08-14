<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Notorix') }}</title>

        <meta name="author" content="Thesis asbl">
        <meta name="description" content="{{ __("User management tool for schools") }}">
        <meta name="keywords" content="thesix,repertoire,data mining,rgpd,gdpr">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @if ($vue ?? '')
        <script src="{{ mix('js/app-vue.js') }}" defer></script>
        @else
        <script src="{{ mix('js/app.js') }}" defer></script>
        @endif

    </head>
    <body>
        <div class="font-sans text-primary antialiased">
            {{ $slot }}
        </div>
    </body>
    @if(config('perso.tawk_show_guest'))
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
            var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
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
</html>
