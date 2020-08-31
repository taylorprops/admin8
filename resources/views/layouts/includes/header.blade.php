@unless (Auth::check())
    @php header('Location: /'); exit(); @endphp
@endunless
@if(!auth() -> user())
    @php header('Location: /'); exit(); @endphp
@endif

<!--Double navigation-->
<header>
    <!-- Sidebar navigation -->
    <div id="slide-out" class="side-nav bg-primary">
        <ul class="custom-scrollbar menu-ul">
            <!-- Logo -->
            {{-- <li>
                <div class="waves-light">
                    <a class="header-logo-link text-center" href="javascript: void(0)"><img src="{{ \Session::get('header_logo_src') }}" class="header-logo"></a>
                </div>
            </li> --}}
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
                <a href="#" data-activates="slide-out" class="button-collapse"><i class="fas fa-bars fa-lg"></i></a>
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
</header>
<!--/.Double navigation-->


