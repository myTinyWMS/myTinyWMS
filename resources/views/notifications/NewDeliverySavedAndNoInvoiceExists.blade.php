<li class="flex">
    <div class="w-11/12">
        <a href="{{ route('order.show', [$notification->data['delivery']->order]) }}">
            <div class="text-gray-800">
                <i class="fa fa-envelope fa-fw"></i> Neue Lieferung ohne Rechnung
                <span class="right-0 text-gray-600 text-xs">{{ $notification->created_at->diffForHumans(\Carbon\Carbon::now()) }}</span>
            </div>
        </a>
    </div>
    <div class="w-1/12">
        <button type="button" class="btn btn-xs btn-danger delete-notification" data-id="{{ $notification->id }}"><i class="fa fa-remove"></i></button>
    </div>
</li>