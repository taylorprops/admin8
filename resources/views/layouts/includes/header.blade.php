@unless (Auth::check())
    @php header('Location: /'); exit(); @endphp
@endunless
<nav class="navbar sticky-top navbar-expand-lg bg-primary w-100">
    <div class="menu">
        <div class="nav-layer"></div>
        <div class="nav-menu-container">
            @if(auth() -> user() -> group == 'admin')
                @include('layouts.includes/menus/admin')
            @elseif(auth() -> user() -> group == 'agent')
                @include('layouts.includes/menus/agent')
            @endif
        </div>
    </div>
    <div class="d-flex justify-content-between w-100">
        <a class="nav-toggle ml-1 ml-md-3">
            <span></span>
            <span></span>
            <span></span>
        </a>
        <a href="/dashboard"><img class="logo" src="/images/logo/logos.png"></a>
        <a href="/logout" class="text-white mr-1 mr-md-3 mt-1">Logout</a>
    </div>
</nav>

