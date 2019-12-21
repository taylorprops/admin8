<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <!-- Collapse button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_main_menu"
        aria-controls="navbar_main_menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible content -->
    <div class="collapse navbar-collapse d-flex justify-content-between" id="navbar_main_menu">

        <div class="mr-auto">
            <!-- Links -->
            <ul class="navbar-nav">

                <li class="nav-item dropdown mega-dropdown active mr-5">
                    <a class="nav-link dropdown-toggle text-uppercase" id="navbar_menu_link" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false"><i class="fal fa-bars fa-2x text-white"></i>
                    </a>
                    <div class="dropdown-menu mega-menu v-2 z-depth-1 bg-blue-light py-5 px-3"
                        aria-labelledby="navbar_menu_link">
                        <div class="row">
                            <div class="col-md-6 col-xl-3 mb-xl-0 mb-4">
                                <h6 class="sub-title text-uppercase font-weight-bold text-primary">Create Docs</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <a class="menu-item pl-0" href="/doc_management/create/upload/files">
                                            <i class="fas fa-caret-right pl-2 pr-3"></i>View/Add Uploaded Files
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-xl-0 mb-4">
                                <h6 class="sub-title text-uppercase font-weight-bold text-primary">Fill Docs</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <a class="menu-item pl-0" href="/doc_management/create/fill/fillable_files">
                                            <i class="fas fa-caret-right pl-2 pr-3"></i>Fillable Files
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-md-0 mb-xl-0 mb-4">

                            </div>
                            <div class="col-md-6 col-xl-3 mb-0">

                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <!-- Navbar brand -->
                    <a class="navbar-brand text-uppercase" href="/dashboard"><img src="/images/logo/logo.png" height="40"></a>
                </li>
            </ul>
            <!-- Links -->

        </div>

        <!-- Search form -->
        <div class="mx-auto">
            <input class="form-input-search" id="main_search" type="text" placeholder="Search" aria-label="Search">
        </div>

        <div class="ml-auto"><a href="/logout" class="text-white">Logout</a></div>

    </div>
    <!-- Collapsible content -->

</nav>
<!-- Navbar -->



{{-- <nav class="navbar navbar-expand-lg bg-primary">
    <!-- SideNav slide-out button -->
    <a href="#" data-activates="slide-out" class="button-collapse"><i class="fal fa-bars fa-2x text-white"></i></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon text-white"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">

    </div>
    <div class="float-right"><a href="/logout" class="text-white">Logout</a></div>
</nav>

<!-- Sidebar navigation -->
<div id="slide-out" class="side-nav">
    <ul class="custom-scrollbar">
        <li>
            <div class="logo-wrapper waves-light">
                <a href="#"><img src="https://mdbootstrap.com/img/logo/mdb-transparent.png"
                        class="img-fluid flex-center"></a>
            </div>
        </li>
        <!--Search Form-->
        <li>
            <form class="search-form" role="search">
                <div class="form-group md-form mt-0 pt-1 waves-light">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
            </form>
        </li>
        <!--/.Search Form-->
        <li>
            <ul class="collapsible collapsible-accordion">
                <li><a class="collapsible-header waves-effect arrow-r"><i class="fas fa-chevron-right"></i> Create Docs <i class="fas fa-angle-down rotate-icon"></i></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a class="waves-effect" href="/doc_management/create/upload">Add Files</a></li>
                            <li><a class="waves-effect" href="/doc_management/create/upload/files">Uploaded Files</a></li>
                            </li>
                        </ul>
                    </div>
                </li>
                <li><a class="collapsible-header waves-effect arrow-r"><i class="fas fa-chevron-right"></i> Fill Files <i class="fas fa-angle-down rotate-icon"></i></a>
                    <div class="collapsible-body">
                        <ul>
                            <li><a class="waves-effect" href="/doc_management/create/fill/fillable_files">Fillable Files</a></li>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </li>
    </ul>
    <div class="sidenav-bg rgba-blue-strong"></div>
</div>
<!--/. Sidebar navigation -->
 --}}
