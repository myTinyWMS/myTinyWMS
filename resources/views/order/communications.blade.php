@if($messages->count() == 0 && $order->supplier->email && $order->status == \Mss\Models\Order::STATUS_NEW)
    <a href="{{ route('order.message_create', ['order' => $order, 'sendorder' => 1]) }}" class="btn btn-lg btn-success">Bestellung per E-Mail an Lieferant schicken</a>
@endif

<div class="row flex">
    <div class="w-64 border-r">
        @foreach($messages as $message)
            <div class="flex flex-col py-2 pr-4 border-b">
                <div class="flex">
                    <div class="w-1/2 font-bold text-sm" title="{{ optional($message->user)->name }}">
                        {{ $message->sender->contains('System') ? 'System' : 'Lieferant' }}
                    </div>
                    <div class="w-1/2 text-xs text-gray-500 text-right" title="{{ $message->received->format('d.m.Y H:i:s') }}">
                        {{ $message->received->format('d.m.Y') }}
                    </div>
                </div>
                <div class="text-sm mt-4">
                    {{ $message->subject }}
                    @if(!$message->read)
                        <span class="label label-primary">NEU</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex-1 px-4">
        @foreach($messages->reverse()->take(1) as $message)
            <div class="flex">
                <div class="text-xs text-gray-500 flex-1">
                    <z icon="time" class="fill-current w-3 h-3 inline-block"></z> {{ $message->received->formatLocalized('%A, %d.%B %Y, %H:%M Uhr') }}
                    @if ($message->sender->contains('System'))
                        von {{ $message->user ? $message->user->name : 'System' }} an {{ $message->receiver->implode(', ') }}
                    @else
                        von {{ $message->sender->implode(', ') }}
                    @endif
                </div>
                <dot-menu>
                    <a href="{{ route('order.message_forward_form', [$message]) }}" title="Weiterleiten"><i class="fa fa-forward"></i> Weiterleiten</a>
                    <a href="{{ route('order.message_create', ['order' => $order, 'answer' => $message->id]) }}"><i class="fa fa-reply"></i> Antworten</a>

                    @if(!$message->read)
                        <a href="{{ route('order.message_read', [$order, $message]) }}" title="Als Gelesen markieren"><i class="fa fa-eye"></i> Gelesen</a>
                    @else
                        <a href="{{ route('order.message_unread', [$order, $message]) }}" title="Als Ungelesen markieren"><i class="fa fa-eye"></i> Ungelesen</a>
                    @endif

                    <a href="#" title="In Bestellung verschieben" @click="$modal.show('assignOrderMessageModal', {message_id: {{ $message->id }}})"><i class="fa fa-share"></i> Verschieben</a>
                    <a href="{{ route('order.message_delete', ['message' => $message, 'order' => $order]) }}" onclick="return confirm('Wirklich löschen?')" title="Nachricht löschen"><i class="fa fa-trash-o"></i> Löschen</a>
                </dot-menu>
            </div>

            <h1 class="my-2 pm-2 border-b">{{ $message->subject }}</h1>

            @if (!empty($message->htmlBody))
                <iframe seamless frameborder="0" class="w-full h-screen" srcdoc="{!! htmlspecialchars($message->htmlBody) !!}"></iframe>
            @else
                <div class="w-full h-screen">
                    {!! nl2br(strip_tags($message->textBody)) !!}
                </div>
            @endif

            @if($message->attachments->count())
                <div class="mt-4 border-t pt-4">
                    <div class="text-xs mb-4">
                        <z icon="attachment" class="fill-current w-3 h-3 inline-block"></z> {{ $message->attachments->count() }} {{ trans_choice('plural.attachment', $message->attachments->count()) }}:
                    </div>

                    <div class="flex">
                    @foreach($message->attachments as $attachment)
                        <a href="{{ route('order.message_attachment_download', [$message->id, $attachment['fileName']]) }}" class="block border flex flex-col items-center p-4 mr-4 hover:bg-gray-400">
                            <z icon="document" class="fill-current w-8 h-8 mb-4"></z>
                            <div class="text-sm">{{ iconv_mime_decode($attachment['orgFileName']) }}</div>
                        </a>
                    @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>



<assign-order-message-modal>{!! $dataTable->table() !!}</assign-order-message-modal>

<data-tables-filter>
    <data-tables-filter-select label="Status" col-id="2">
        <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
        <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
        <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
        <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
        <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
        <option value="{{ \Mss\Models\Order::STATUS_PAID }}">bezahlt</option>
        <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">storniert</option>
    </data-tables-filter-select>
</data-tables-filter>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function () {
            $('#assignMessageModal').on('shown.bs.modal', function (e) {
                $('#message').val($(e.relatedTarget).attr('data-message-id'));
            })
        });
    </script>
@endpush