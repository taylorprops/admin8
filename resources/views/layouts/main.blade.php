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
        {{-- <link href="/resources/mdbootstrap/css/addons-pro/timeline.min.css" rel="stylesheet"> --}}
        {{-- mdbootsrap stepper --}}
        <link href="/resources/mdbootstrap/css/addons-pro/steppers.min.css" rel="stylesheet">


        <script src="/js/app.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

        {{-- mdbootsrap --}}
        <script type="text/javascript" src="/resources/mdbootstrap/js/popper.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/mdb.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/addons/datatables.min.js"></script>
        {{-- mdbootsrap stepper --}}
        <script type="text/javascript" src="/resources/mdbootstrap/js/addons-pro/steppers.min.js"></script>

        {{-- page transitions --}}
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.16.1/TweenMax.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.12.1/plugins/CSSRulePlugin.min.js"></script>

        {{-- text editor --}}
        <script src="https://cdn.tiny.cloud/1/t3u7alod16y8nsqt07h4m5kwfw8ob9sxbvy2rlmrqo94zrui/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

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

    <body class="hidden-sn @if(Request::is('*/edit_files/*')) y-scroll-none @endif @if(Request::is('*/document_review')) overflow-y-hidden @endif">

        @include('layouts.includes.header')

        {{-- page transitions --}}
        {{-- <div class="loader">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
            <div class="bar4"></div>
            <div class="bar5"></div>
            <div class="bar6"></div>
        </div> --}}

        <main>
        @yield('content')
        </main>

        @include('layouts.includes.common_includes.modals.modals')

    </body>

</html>
