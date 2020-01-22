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

        <main>
        @yield('content')
        </main>

        @include('layouts.includes.common_includes.modals.modals')

        <script src="/js/app.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/popper.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/mdb.min.js"></script>



        @yield('js')

    </body>

</html>
