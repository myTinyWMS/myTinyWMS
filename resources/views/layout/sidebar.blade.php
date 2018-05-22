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
                <a href="{{ url('/dashboard') }}" title="Dashboard">
                    <i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['article*'])) }}">
                <a href="{{ url('/article') }}" title="Artikel">
                    <i class="fa fa-file"></i> <span class="nav-label">Artikel</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['order*'])) }}">
                <a href="{{ url('/order') }}" title="Bestellungen">
                    <i class="fa fa-shopping-cart"></i> <span class="nav-label">Bestellungen</span>
                    @if($globalPageService->getUnreadMessageCount())
                        <div class="label label-primary pull-right" title="{{ $globalPageService->getUnreadMessageCount() }} ungelesene {{ trans_choice('plural.message', $globalPageService->getUnreadMessageCount()) }}">{{ $globalPageService->getUnreadMessageCount() }}</div>
                    @endif
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['supplier*'])) }}">
                <a href="{{ url('/supplier') }}" title="Lieferanten">
                    <i class="fa fa-truck"></i> <span class="nav-label">Lieferanten</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['category*'])) }}">
                <a href="{{ url('/category') }}" title="Kategorien">
                    <i class="fa fa-th-list"></i> <span class="nav-label">Kategorien</span>
                </a>
            </li>
            <li class="{{ active_class(if_uri_pattern(['unit*'])) }}">
                <a href="{{ url('/unit') }}" title="Einheiten">
                    <i class="fa fa-th-list"></i> <span class="nav-label">Einheiten</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/reports') }}" title="Reports">
                    <i class="fa fa-table"></i> <span class="nav-label">Reports</span>
                </a>
            </li>
        </ul>

    </div>
</nav>