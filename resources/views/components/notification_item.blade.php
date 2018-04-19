<li>
    <div class="dropdown-messages-box">
        <div class="media-body">
            <small class="pull-right"><button aria-hidden="true" data-dismiss="alert" class="close pull-right delete-notification" type="button" data-id="{{ $notification->id }}">×</button></small>
            @include('notifications.'.last(explode('\\', $notification->type)), compact('notification'))<br/>
            <small class="text-muted">
                 am {{ $notification->created_at->timezone(Auth::user()->timezone)->format("d.m.Y H:i") }}
            </small>
        </div>
    </div>
</li>
