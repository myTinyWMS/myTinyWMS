<li>
    <a href="{{ route('order.show', [$notification->data['order']]) }}">
        <div>
            <i class="fa fa-envelope fa-fw"></i> Neue Lieferung zu vorhandener Rechnung
            <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans(\Carbon\Carbon::now()) }}</span>
        </div>
    </a>
</li>