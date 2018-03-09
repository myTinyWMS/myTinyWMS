<div class="fh-breadcrumb">
    <div class="fh-column">
        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 100%;">
            <div class="full-height-scroll" style="overflow: hidden; width: auto; height: 100%;">
                <ul class="list-group elements-list">
                    @foreach($messages as $message)
                        <li class="list-group-item">
                            <a data-toggle="tab" href="#tab-{{ $loop->iteration }}">
                                <small class="pull-right text-muted" title="{{ $message->received->format('d.m.Y H:i:s') }}"> {{ $message->received->format('d.m.Y') }}</small>
                                <strong title="{{ optional($message->user)->name }}">{{ $message->sender->contains('System') ? 'System' : 'Lieferant' }}</strong>
                                <div class="small m-t-xs">
                                    <p class="m-b-xs">{{ $message->subject }}</p>
                                    @if(!$message->read)
                                    <p class="m-b-none">
                                        <span class="label pull-right label-primary">NEU</span>
                                    </p>
                                    @endif
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
                        @foreach($messages as $message)
                            <div id="tab-{{ $loop->iteration }}" class="tab-pane @if($loop->first) active @endif">
                                <div class="pull-right">
                                    <div class="tooltip-demo">
                                        <button class="btn btn-white btn-xs" data-toggle="tooltip" data-placement="bottom" title="auf Nachricht antworten"><i class="fa fa-reply"></i> Antworten</button>
                                        @if(!$message->read)
                                        <button class="btn btn-white btn-xs" data-toggle="tooltip" data-placement="bottom" title="Als Gelesen markieren"><i class="fa fa-eye"></i> Gelesen</button>
                                        @else
                                        <button class="btn btn-white btn-xs" data-toggle="tooltip" data-placement="bottom" title="Als Ungelesen markieren"><i class="fa fa-eye"></i> Ungelesen</button>
                                        @endif
                                        {{--<button class="btn btn-white btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="Mark as important"><i class="fa fa-exclamation"></i> </button>--}}
                                        <button class="btn btn-white btn-xs" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Nachricht löschen"><i class="fa fa-trash-o"></i> Löschen</button>
                                    </div>
                                </div>
                                <div class="small text-muted">
                                    <i class="fa fa-clock-o"></i> {{ $message->received->formatLocalized('%A, %d.%B %Y, %H:%M Uhr') }}
                                    @if ($message->sender->contains('System'))
                                        von {{ $message->user ? $message->user->name : 'System' }} an {{ $message->receiver->implode(', ') }}
                                    @else
                                        von {{ $message->sender->implode(', ') }}
                                    @endif
                                </div>

                                <h1>{{ $message->subject }}</h1>

                                <iframe seamless frameborder="0" class="full-width" srcdoc='{!! $message->htmlBody  !!}'></iframe>

                                @if($message->attachments->count())
                                <div class="m-t-lg">
                                    <p>
                                        <span><i class="fa fa-paperclip"></i> {{ $message->attachments->count() }} {{ ($message->attachments->count() === 1 ? 'Anhang' : 'Anhänge') }}{{-- - --}}</span>
                                        {{--<a href="#">Download all</a>
                                        |
                                        <a href="#">View all images</a>--}}
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