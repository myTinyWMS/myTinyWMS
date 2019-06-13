@extends('layout.app')

@section('title', 'Neue Nachrichten')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Bestellungen</a>
    </li>
    <li class="active">
        <strong>Neue Nachrichten</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="card w-full">
            <div class="card-content">
                <order-messages :messages="{{ $unassignedMessages }}"></order-messages>
            </div>
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

    {{--<div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>
                        Nicht zugeordnete neue Nachrichten
                    </h5>
                </div>
                <div class="ibox-content">
                    <div class="fh-breadcrumb">
                        <div class="fh-column">
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
                                <div class="full-height-scroll" style="overflow: hidden; width: auto; height: 100%;">
                                    <ul class="list-group elements-list">
                                        @foreach($unassignedMessages as $message)
                                            <li class="list-group-item">
                                                <a data-toggle="tab" href="#tab-{{ $loop->iteration }}">
                                                    <small class="pull-right text-muted" title="{{ $message->received->format('d.m.Y H:i:s') }}"> {{ $message->received->format('d.m.Y') }}</small>
                                                    <strong title="{{ optional($message->user)->name }}">{{ $message->sender->contains('System') ? 'System' : 'Lieferant' }}</strong>
                                                    <div class="small m-t-xs">
                                                        <p class="m-b-xs">{{ $message->subject }}</p>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="slimScrollBar" style="background: rgb(0, 0, 0) none repeat scroll 0 0; width: 7px; position: absolute; top: 0; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 536.965px;"></div>
                                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                            </div>
                        </div>

                        <div class="full-height">
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
                                <div class="full-height-scroll white-bg border-left" style="overflow: hidden; width: auto; height: 100%;">
                                    <div class="element-detail-box">
                                        <div class="tab-content">
                                            @foreach($unassignedMessages as $message)
                                                <div id="tab-{{ $loop->iteration }}" class="tab-pane @if($loop->first) active @endif">
                                                    <div class="pull-right">
                                                        <div class="tooltip-demo">
                                                            <div class="btn-group">
                                                                <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="true">Aktionen <span class="caret"></span></button>
                                                                <ul class="dropdown-menu dropdown-menu-right">
                                                                    <li><a href="{{ route('order.message_forward_form', [$message]) }}" title="Weiterleiten"><i class="fa fa-forward"></i> Weiterleiten</a></li>
                                                                    <li><a href="#" title="In Bestellung verschieben" data-message-id="{{ $message->id }}" data-toggle="modal" data-target="#assignMessageModal"><i class="fa fa-share"></i> Verschieben</a></li>
                                                                    <li><a href="{{ route('order.message_delete', ['message' => $message]) }}" onclick="return confirm('Wirklich löschen?')" title="Nachricht löschen"><i class="fa fa-trash-o"></i> Löschen</a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <i class="fa fa-clock-o"></i> {{ $message->received->formatLocalized('%A, %d.%B %Y, %H:%M Uhr') }}
                                                        @if ($message->sender->contains('System'))
                                                            von {{ $message->user ? $message->user->name : 'System' }} an {{ $message->receiver->implode(', ') }}
                                                        @else
                                                            von {{ $message->sender->implode(', ') }} {!! $message->getSupplierBySender() ? '<span class="text-danger">('.$message->getSupplierBySender()->name.'?)</span>' : '' !!}
                                                        @endif
                                                    </div>

                                                    <h1>{{ $message->subject }}</h1>

                                                    @if (!empty($message->htmlBody))
                                                        <iframe seamless frameborder="0" class="full-width" height="600" srcdoc="{!! htmlspecialchars($message->htmlBody) !!}"></iframe>
                                                    @else
                                                        {!! nl2br(strip_tags($message->textBody)) !!}
                                                    @endif

                                                    @if($message->attachments->count())
                                                        <div class="m-t-lg">
                                                            <p>
                                                                <span><i class="fa fa-paperclip"></i> {{ $message->attachments->count() }} {{ trans_choice('plural.attachment', $message->attachments->count()) }}--}}{{-- - --}}{{--</span>
                                                                --}}{{--<a href="#">Download all</a>
                                                                |
                                                                <a href="#">View all images</a>--}}{{--
                                                            </p>

                                                            <div class="attachment">
                                                                @foreach($message->attachments as $attachment)
                                                                    <div class="file-box">
                                                                        <div class="file">
                                                                            <a href="{{ route('order.message_attachment_download', [$message->id, $attachment['fileName']]) }}">
                                                                                <span class="corner"></span>

                                                                                <div class="icon">
                                                                                    <i class="fa fa-file"></i>
                                                                                </div>
                                                                                <div class="file-name">
                                                                                    {{ $attachment['orgFileName'] }}
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="slimScrollBar" style="background: rgb(0, 0, 0) none repeat scroll 0% 0%; width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 728px;"></div>
                                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51) none repeat scroll 0% 0%; opacity: 0.2; z-index: 90; right: 1px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal modal-wide fade" id="assignMessageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['route' => ['order.message_assign'], 'method' => 'POST']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Nachricht zordnen</h4>
                </div>
                <div class="modal-body">
                    {!! $dataTable->table() !!}
                </div>
                <div class="modal-footer">
                    {!! Form::hidden('message', '', ['id' => 'message']) !!}
                    <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-success">Speichern</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <data-tables-filter>
        <data-tables-filter-select label="Status" col-id="3">
            <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">bezahlt</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">storniert</option>
        </data-tables-filter-select>
    </data-tables-filter>--}}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush