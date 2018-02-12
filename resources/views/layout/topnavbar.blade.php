<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="">
                <div class="form-group">
                    <input placeholder="Suche ..." class="form-control" name="top-search" id="top-search" type="text">
                </div>
            </form>
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
</div>

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