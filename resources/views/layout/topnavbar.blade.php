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
@endpush