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
        <a class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </a>
        <a href="/dashboard"><img src="/images/logo/logos.png" height="30"></a>
        <a href="/logout" class="text-white mr-3 mt-1">Logout</a>
    </div>
</nav>

