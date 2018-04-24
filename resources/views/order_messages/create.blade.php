@extends('layout.app')

@section('title', 'Neue Nachricht')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li>
        <a href="{{ route('order.show', $order) }}">Bestelldetails</a>
    </li>
    <li class="active">
        <strong>Neue Nachricht</strong>
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
        context.invoke('editor.pasteHTML', `@include('components.signature')`);
    }
    });

    return button.render();   // return button as jquery object
};
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-8">
        {!! Form::open(['route' => ['order.message_create', $order], 'method' => 'POST', 'id' => 'newMessageForm']) !!}
        <div class="ibox">
            <div class="ibox-title">
                <h5>Neue Nachricht an {{ $order->supplier->name }}</h5>
            </div>
            <div class="ibox-content">
                {{ Form::bsText('receiver', $preSetReceiver ?: $order->supplier->email, [], 'Empfänger') }}
                {{ Form::bsText('subject', $preSetSubject, [], 'Betreff') }}
                {{ Form::summernote('body', $preSetBody, [], 'Nachricht') }}

                <hr class="hr-line-solid">
                {!! Form::hidden('attachments') !!}
                {!! Form::hidden('sendOrder', $sendOrder) !!}
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
                {{ Form::dropzone('attachments', 'Anhänge', route('order.message_upload', $order)) }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var attachments = [];
    Dropzone.options.dropzoneForm = {
        init: function() {
            this.on("complete", function(event) {
                var file = {
                    'tempFile': JSON.parse(event.xhr.response),
                    'orgName': event.name,
                    'type': event.type
                };

                attachments.push(file);
            });
        }
    };

    $(document).ready(function () {
        $('#newMessageForm').submit(function() {
            $('input[name="attachments"]').val(JSON.stringify(attachments));
        });
    });
</script>
@endpush