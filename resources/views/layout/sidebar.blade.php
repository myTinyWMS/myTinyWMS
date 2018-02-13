<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="profile-element">MSS</div>
                <div class="logo-element">
                    MSS
                </div>
            </li>
            <li class="{{ active_class(if_uri(['dashboard', '/'])) }}">
                <a href="{{ url('/dashboard') }}">
                    <i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['article*'])) }}">
                <a href="{{ url('/article') }}">
                    <i class="fa fa-file"></i> <span class="nav-label">Artikel</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['order*'])) }}">
                <a href="{{ url('/order') }}">
                    <i class="fa fa-shopping-cart"></i> <span class="nav-label">Bestellungen</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['supplier*'])) }}">
                <a href="{{ url('/supplier') }}">
                    <i class="fa fa-truck"></i> <span class="nav-label">Lieferanten</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['category*'])) }}">
                <a href="{{ url('/category') }}">
                    <i class="fa fa-th-list"></i> <span class="nav-label">Kategorien</span>
                </a>
            </li>
        </ul>

    </div>
</nav>