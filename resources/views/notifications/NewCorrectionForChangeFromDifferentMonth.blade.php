<li>
    <div class="col-lg-11">
        <a href="{{ route('article.show', [$notification->data['article']]) }}">
            <div>
                <i class="fa fa-envelope fa-fw"></i> Neue Korrektur zu Buchung aus anderem Monat
                <span class="pull-right text-muted small">{{ $notification->created_at->diffForHumans(\Carbon\Carbon::now()) }}</span>
            </div>
        </a>
    </div>
    <div class="col-lg-1">
        <button type="button" class="btn btn-xs btn-danger delete-notification" data-id="{{ $notification->id }}"><i class="fa fa-remove"></i></button>
    </div>
</li>