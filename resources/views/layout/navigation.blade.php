<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ Auth::user()->name }}</strong>
                            </span>
                            {{--<span class="text-muted text-xs block">Example menu <b class="caret"></b></span>--}}
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                        </li>
                    </ul>
                </div>
                <div class="logo-element">
                    MAIL
                </div>
            </li>

            <li class="{{ active_class(if_uri(['dashboard'])) }}">
                <a href="{{ url('/dashboard') }}">
                    <i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span>
                </a>
            </li>
        </ul>

    </div>
</nav>