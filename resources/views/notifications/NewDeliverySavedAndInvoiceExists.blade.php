<li>
    <div class="col-lg-11">
        <a href="{{ route('order.show', [$notification->data['order']]) }}">
            <div>
                <i class="fa fa-envelope fa-fw"></i> Neue Lieferung zu vorhandener Rechnung
                <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans(\Carbon\Carbon::now()) }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-1">
        <button type="button" class="btn btn-xs btn-danger delete-notification" data-id="{{ $notification->id }}"><i class="fa fa-remove"></i></button>
    </div>
</li>