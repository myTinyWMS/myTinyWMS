<div class="w-full fixed pin-l pin-y z-50">
    {{-- Row 1 --}}
    <div class="bg-white w-full h-header flex items-center px-view">
        <div class="flex-1 pr-6 text-blue-900 flex">
            <z icon="factory" class="fill-current w-6 h-6"></z>
            <div class="ml-2 text-xl font-bold">MSS</div>
        </div>
        <div class="relative z-50 w-full max-w-xs">
            <div class="relative">
                <div class="relative">
                    <label class="search"><input type="search" placeholder="Suche" class="form-control form-input form-input-bordered w-full shadow"></label>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2 --}}
    <div class="flex relative shadow-md h-header bg-white px-view z-20 border-t border-gray-300 topbar-nav">
        <div class="flex-1 flex items-center">
            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri(['dashboard', '/'])) }}">
                <a href="{{ url('/dashboard') }}">
                    Dashboard
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['article*'])) }}">
                <a href="{{ url('/article') }}">
                    Artikel
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['order*'])) }}">
                <a href="{{ url('/order') }}">
                    Bestellungen
                    @if($globalPageService->getUnreadMessageCount())
                        <div class="label ml-2 inline-block bg-blue-900 text-white rounded-full px-2 w-6 h-6" title="{{ $globalPageService->getUnreadMessageCount() }} ungelesene {{ trans_choice('plural.message', $globalPageService->getUnreadMessageCount()) }}">{{ $globalPageService->getUnreadMessageCount() }}</div>
                    @endif
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['supplier*'])) }}">
                <a href="{{ url('/supplier') }}">
                    Lieferanten
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['category*'])) }}">
                <a href="{{ url('/category') }}">
                    Kategorien
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['unit*'])) }}">
                <a href="{{ url('/unit') }}">
                    Einheiten
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['report*'])) }}">
                <a href="{{ url('/reports') }}">
                    Reports
                </a>
            </h3>

            <h3 class="mr-6 h-full pt-5 {{ active_class(if_uri_pattern(['inventory*'])) }}">
                <a href="{{ url('/inventory') }}">
                    Inventur
                </a>
            </h3>

            {{--<div class="dropdown relative ml-auto h-9 flex items-center dropdown-right">
                <a class="dropdown-trigger h-dropdown-trigger flex items-center cursor-pointer select-none h-9 flex items-center">

                </a>
            </div>--}}
        </div>
        <div class="flex items-center">
            <a class="dropdown-toggle count-info mr-8 relative" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="fa fa-bell"></i>  <span class="ml-2 inline-block text-white rounded-full w-5 h-5 absolute @if(count($globalPageService->getNotifications())) bg-red-700 @else bg-blue-700 @endif notification-label">{{ count($globalPageService->getNotifications()) }}</span>
            </a>

            <a href="#" class="dropdown-toggle mr-8" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs"></i> <span class="caret"></span></a>

            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i> Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
        </div>
    </div>
</div>

{{--
<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-outline" href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="">
                <div class="form-group">
                    <input placeholder="Suche ..." class="form-control typeahead" name="top-search" id="top-search" data-provide="typeahead" type="text">
                </div>
            </form>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-bell"></i>  <span class="label @if(count($globalPageService->getNotifications())) label-danger @else label-default @endif">{{ count($globalPageService->getNotifications()) }}</span>
                </a>
                <ul class="dropdown-menu dropdown-alerts">
                    @if(count($globalPageService->getNotifications()))
                        @foreach($globalPageService->getNotifications() as $notification)
                            @include('notifications.'.last(explode('\\', $notification->type)), compact('notification'))
                        @endforeach
                    @else
                        <li>
                            <div>Keine Nachrichten vorhanden</div>
                        </li>
                    @endif
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs"></i> <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('settings.show') }}">Einstellungen</a></li>
                    <li><a href="{{ route('settings.change_pw') }}">Passwort ändern</a></li>
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
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $('#top-search').typeahead({
            source:  function (query, process) {
                return $.post('{{ route('global_search') }}', { query: query }, function (data) {
                    return process(data);
                });
            },
            displayText: function (item) {
                return item.name
            },
            afterSelect: function (selected) {
                window.location.href = selected.link;
            },
            minLength: 3
        });

        $('.delete-notification').click(function (event) {
            event.stopImmediatePropagation();
            var notificationItem = $(this).parent().parent();
            $.get('/notification/' + $(this).attr('data-id') + '/delete', function () {
                notificationItem.remove();

                var current = $('.count-info > .label')[0].innerText;
                current--;
                $('.count-info > .label')[0].innerText = current;

                if (current == 0) {
                    $('.count-info .label').removeClass('label-danger');
                    $('.count-info .label').addClass('label-default');
                }
            });
        });
    });
</script>
@endpush--}}
