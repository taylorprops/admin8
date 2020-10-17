<header>

    <nav class="navbar navbar-expand-xl fixed-top navbar-sticky navbar-dark bg-primary navbar-hover" id="main_nav_bar">

        <a class="header-logo-link text-center" href="javascript: void(0)"><img src="{{ \Session::get('header_logo_src') }}" class="header-logo"></a>

        <div class="mr-5">
            <input class="main-search-input top" type="text" placeholder="Search" aria-label="Search">
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav_collapse" aria-controls="main_nav_collapse" aria-expanded="false" aria-label="Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="main_nav_collapse">
            <ul class="navbar-nav">

                @if(auth() -> user() -> group == 'admin')

                {{-- @include('layouts.includes/menus/admin') --}}
                <li class="nav-item">
                    <a class="nav-link" href="javascript: void(0)">Link</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript: void(0)" id="navbarDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="javascript: void(0)">Action</a></li>
                        <li><a class="dropdown-item" href="javascript: void(0)">Another action</a></li>
                        <div class="dropdown-divider"></div>
                        <li></li><a class="dropdown-item" href="javascript: void(0)">Something else here</a></li>
                        <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="navbarDropdown1" role="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Dropdown
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                                    <li><a class="dropdown-item" href="javascript: void(0)">Action</a></li>
                                    <li><a class="dropdown-item" href="javascript: void(0)">Another action</a></li>
                                    <div class="dropdown-divider"></div>
                                    <li></li><a class="dropdown-item" href="javascript: void(0)">Something else here</a></li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-item dropdown-toggle" href="javascript: void(0)" id="navbarDropdown2" role="button" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Left Dropdown
                                        </a>
                                        <ul class="dropdown-menu left" aria-labelledby="navbarDropdown2">
                                            <li><a class="dropdown-item" href="javascript: void(0)">Action</a></li>
                                            <li><a class="dropdown-item" href="javascript: void(0)">Another action</a></li>
                                            <div class="dropdown-divider"></div>
                                            <li></li><a class="dropdown-item" href="javascript: void(0)">Something else here</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                    </ul>
                </li>

                @elseif(auth() -> user() -> group == 'agent')

                @include('layouts.includes/menus/agent')

                @endif

            </ul>

            <div class="ml-5">
                <input class="main-search-input bottom" type="text" placeholder="Search" aria-label="Search">
            </div>

            <ul class="navbar-nav w-100">
                <li class="w-100">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="mr-5">
                            <a class="nav-link text-white" href="javascript: void(0)"><i class="far fa-comments mr-2"></i> <span class="clearfix d-none d-sm-inline-block">Support</span></a>
                        </div>
                        <div>
                            <a class="nav-link text-white py-0" href="javascript: void(0)"><i class="fas fa-user mr-2"></i> <span class="clearfix d-none d-sm-inline-block">{{ ucwords(auth() -> user() -> group).' - '.auth() -> user() -> name }}</span></a>
                            <a class="nav-link text-white py-0 float-right" href="/logout"><span class="clearfix d-none d-sm-inline-block">Logout</span></a>
                        </div>
                    </div>
                </li>
            </ul>



        </div>
    </nav>
</header>


{{-- <header>
    <nav class="navbar navbar-expand-lg scrolling-navbar navbar-dark bg-primary sticky-top mr-0">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav_collapse" aria-controls="main_nav_collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">Menu</span>
        </button>
        <div class="collapse navbar-collapse" id="main_nav_collapse">
            <a class="navbar-brand text-center" href="javascript: void(0)"><img src="{{ \Session::get('header_logo_src') }}" class="header-logo"></a>
<ul class="navbar-nav w-100 mt-2 mt-lg-0">
    @if(auth() -> user() -> group == 'admin')

    @include('layouts.includes/menus/admin')

    @elseif(auth() -> user() -> group == 'agent')

    @include('layouts.includes/menus/agent')

    @endif

    <li class="w-100">
        <ul class="nav float-right">
            <li class="nav-item">
                <a class="nav-link"><i class="far fa-comments"></i> <span class="d-none d-sm-inline-block">Support</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link"><i class="fas fa-user"></i> <span class="d-none d-sm-inline-block">{{ ucwords(auth() -> user() -> group).' - '.auth() -> user() -> name }}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout"><span class="d-none d-sm-inline-block">Logout</span></a>
            </li>
        </ul>
    </li>
</ul>
</div>
</nav>
</header> --}}



{{-- NAVBAR: OLD @unless (Auth::check())
    @php header('Location: /'); exit(); @endphp
@endunless
@if(!auth() -> user())
    @php header('Location: /'); exit(); @endphp
@endif --}}

<!--Double navigation-->
{{-- <header>
    <!-- Sidebar navigation -->
    <div id="slide-out" class="side-nav bg-primary">
        <ul class="custom-scrollbar menu-ul">
            <!-- Logo -->
            <li>
                <div class="waves-light">
                    <a class="header-logo-link text-center" href="javascript: void(0)"><img src="{{ \Session::get('header_logo_src') }}" class="header-logo"></a>
</div>
</li>
<!--/. Logo -->

<hr class="bg-white">

@if(auth() -> user() -> group == 'admin')

@include('layouts.includes/menus/admin')

@elseif(auth() -> user() -> group == 'agent')

@include('layouts.includes/menus/agent')

@endif

</ul>
<div class="sidenav-bg mask-strong"></div>
</div>
<!--/. Sidebar navigation -->
<!-- Navbar -->
<nav class="navbar fixed-top navbar-toggleable-md navbar-expand-lg scrolling-navbar double-nav bg-primary">

    <div class="d-flex justify-content-between w-100 pl-3">

        <!-- SideNav slide-out button -->
        <div>
            <a href="javascript: void(0)" data-activates="slide-out" class="button-collapse"><i class="fas fa-bars fa-lg"></i></a>
        </div>

        <div class="main-search-div">
            <input class="form-input-search" id="main_search_input" type="text" placeholder="Search" aria-label="Search">
        </div>
        <div>
            <ul class="nav navbar-nav nav-flex-icons ml-auto text-white">
                <li class="nav-item">
                    <a class="nav-link"><i class="far fa-comments"></i> <span class="clearfix d-none d-sm-inline-block">Support</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"><i class="fas fa-user"></i> <span class="clearfix d-none d-sm-inline-block">{{ ucwords(auth() -> user() -> group).' - '.auth() -> user() -> name }}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout"><span class="clearfix d-none d-sm-inline-block">Logout</span></a>
                </li>
            </ul>
        </div>

    </div>

</nav>
<!-- /.Navbar -->
</header> --}}
<!--/.Double navigation-->
