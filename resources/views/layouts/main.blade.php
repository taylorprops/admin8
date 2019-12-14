<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'title here')</title>

        <link href="/resources/mdbootstrap/css/mdb.min.css" rel="stylesheet">
        <link href="/css/app.css" rel="stylesheet">
        {{-- <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.11.2/css/all.css" integrity="sha384-zrnmn8R8KkWl12rAZFt4yKjxplaDaT7/EUkKm7AovijfrQItFWR7O/JJn4DAa/gx" crossorigin="anonymous"> --}}
        {{-- <script src="https://kit.fontawesome.com/880f60729b.js" crossorigin="anonymous"></script> --}}
        <link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">


    </head>

    <body>

        @include('layouts.includes.header')

        @yield('content')

        @include('layouts.includes.common_includes.modals.modals')

        <script src="/js/app.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/popper.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/mdb.min.js"></script>


        @yield('js')

    </body>

</html>
