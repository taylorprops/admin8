<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'title here')</title>

        <link href="https://fonts.googleapis.com/css?family=Baskervville|Karma|Lato|Maitree|Roboto&display=swap" rel="stylesheet">
        <link href="/resources/mdbootstrap/css/mdb.min.css" rel="stylesheet">
        <link href="/css/app.css" rel="stylesheet">
        <link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">
        <link href="/resources/mdbootstrap/css/addons/datatables.min.css" rel="stylesheet">

        <script src="/js/app.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/popper.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/mdb.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/addons/datatables.min.js"></script>
        {{-- page transitions --}}
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.16.1/TweenMax.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.12.1/plugins/CSSRulePlugin.min.js"></script>
        {{-- google address search --}}
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('global.vars.google_api_key') }}&libraries=places&outputFormat=json"></script>


        <noscript>
            <style>
              /* simplebar Reinstate scrolling for non-JS clients */
                .simplebar-content-wrapper {
                    overflow: auto;
                }
            </style>
        </noscript>

    </head>

    <body>

        <header>
        @include('layouts.includes.header')
        </header>

        <div class="loader">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
            <div class="bar4"></div>
            <div class="bar5"></div>
            <div class="bar6"></div>
        </div>

        <main>
        @yield('content')
        </main>

        @include('layouts.includes.common_includes.modals.modals')




        @yield('js')

    </body>

</html>
