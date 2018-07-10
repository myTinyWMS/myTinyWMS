@extends('layout.app')

@section('title', 'Nachricht weiterleiten')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Nachricht weiterleiten</strong>
    </li>
@endsection

@section('summernote_custom_toolbar')
['custom', [ 'signature']],
@endsection

@section('summernote_custom_config')
,buttons: {
    signature: SignatureButton
}
@endsection

@section('summernote_custom_js')
var SignatureButton = function (context) {
    var ui = $.summernote.ui;

    // create button
    var button = ui.button({
        contents: '<i class="fa fa-plus"/> Signatur',
        tooltip: 'Signatur einfügen',
        click: function () {
            context.invoke('editor.pasteHTML', `{!! html_entity_decode(Auth::user()->signature) !!}`);
        }
    });

    return button.render();   // return button as jquery object
};
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-8">
        {!! Form::open(['route' => ['order.message_forward', $message], 'method' => 'POST', 'id' => 'forwardMessageForm']) !!}
        <div class="ibox">
            <div class="ibox-title">
                <h5>Nachricht weiterleiten </h5>
            </div>
            <div class="ibox-content">
                {{ Form::bsText('receiver', $preSetReceiver, [], 'Empfänger (mehrere mit Komma getrennt)') }}
                {{ Form::bsText('subject', $preSetSubject, ['disabled' => 'disabled'], 'Betreff') }}

                <label>Nachricht:</label>
                <div class="border-top-bottom border-left-right order-messages">
                    @if (!empty($message->htmlBody))
                        <iframe seamless frameborder="0" class="full-width" height="" srcdoc='{!! htmlspecialchars($message->htmlBody)  !!}'></iframe>
                    @else
                        {!! nl2br($message->textBody) !!}
                    @endif
                </div>

                <hr class="hr-line-solid">
                {!! Form::submit('Abschicken', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="col-lg-4 col-xl-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Anhänge</h5>
            </div>
            <div class="ibox-content">
                @if($message->attachments->count())
                <ul>
                    @foreach($message->attachments as $attachment)
                    <li>{{ $attachment['orgFileName'] }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection