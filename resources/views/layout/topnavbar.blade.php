<div class="row border-bottom white-bg">
    <nav class="navbar navbar-static-top" role="navigation">
        <div class="navbar-header">
            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <i class="fa fa-reorder"></i>
            </button>
            <a href="#" class="navbar-brand">MSS</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
            <ul class="nav navbar-nav">
                <li class="{{ active_class(if_uri(['dashboard', '/'])) }}">
                    <a href="{{ url('/dashboard') }}">
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li class="{{ active_class(if_uri_pattern(['article*'])) }}">
                    <a href="{{ url('/article') }}">
                        <span class="nav-label">Artikel</span>
                    </a>
                </li>
                <li class="{{ active_class(if_uri_pattern(['supplier*'])) }}">
                    <a href="{{ url('/supplier') }}">
                        <span class="nav-label">Lieferanten</span>
                    </a>
                </li>
                <li class="{{ active_class(if_uri_pattern(['category*'])) }}">
                    <a href="{{ url('/category') }}">
                        <span class="nav-label">Kategorien</span>
                    </a>
                </li>
                <li class="dropdown">

                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                        <li><a href="">Menu item</a></li>
                    </ul>
                </li>

            </ul>
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fa fa-bell"></i>  <span class="label @if(count($notifications)) label-primary @else label-default @endif">{{ count($notifications) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                        @if(count($notifications))
                            @foreach($notifications as $notification)
                                @include('components.notification_item', [$notification])
                            @endforeach
                        @else
                            <li>
                                <div>@lang('notifications.empty')</div>
                            </li>
                        @endif
                    </ul>
                </li>
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                </li>
            </ul>
        </div>
    </nav>
</div>

{{--<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            --}}{{--<form role="search" class="navbar-form-custom" method="post" action="/">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search" />
                </div>
            </form>--}}{{--
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-bell"></i>  <span class="label @if(count($notifications)) label-primary @else label-default @endif">{{ count($notifications) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-messages">
                    @if(count($notifications))
                        @foreach($notifications as $notification)
                            @include('components.notification_item', [$notification])
                        @endforeach
                    @else
                        <li>
                            <div>@lang('notifications.empty')</div>
                        </li>
                    @endif
                </ul>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out"></i> Logout
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </li>
        </ul>
    </nav>
</div>--}}

@push('scripts')
<script>
    $(document).ready(function () {
        $('.delete-notification').click(function () {
            var notificationItem = $(this).parent().parent().parent();
            $.get('/notification/' + $(this).attr('data-id') + '/delete', function () {
                notificationItem.remove();

                var current = $('.count-info > .label')[0].innerText;
                current--;
                $('.count-info > .label')[0].innerText = current;

                if (current == 0) {
                    $('.count-info .label').removeClass('label-primary');
                    $('.count-info .label').addClass('label-default');
                }
            });
        });
    });
</script>
@endpush