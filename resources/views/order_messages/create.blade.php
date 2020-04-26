@extends('layout.app')

@section('title', __('Neue Nachricht'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Übersicht')</a>
    </li>
    <li>
        <a href="{{ route('order.show', $order) }}">@lang('Bestelldetails')</a>
    </li>
    <li class="active">
        <strong>@lang('Neue Nachricht')</strong>
    </li>
@endsection

@section('summernote_show_signature_button', true)
@section('summernote_signature', html_entity_decode(Auth::user()->signature))

@section('content')
<div class="row">
    <div class="w-8/12 mr-4">
        {!! Form::open(['route' => ['order.message_create', $order], 'method' => 'POST', 'id' => 'newMessageForm']) !!}
        <div class="card">
            <div class="card-header">
                <h5>@lang('Neue Nachricht an') {{ $order->supplier->name }}</h5>
            </div>
            <div class="card-content">
                {{ Form::bsText('receiver', $preSetReceiver ?: str_replace(';', ',', $order->supplier->email), [], __('Empfänger (mehrere mit Komma getrennt)')) }}
                {{ Form::bsText('subject', $preSetSubject, [], __('Betreff')) }}
                {{ Form::wysiwygEditor('body', $preSetBody, [], __('Nachricht')) }}

                <hr class="hr-line-solid">
                {!! Form::hidden('attachments') !!}
                {!! Form::hidden('sendOrder', $sendOrder) !!}
                {!! Form::submit(__('Abschicken'), ['class' => 'btn btn-primary', 'id' => 'send-message']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="w-4/12">
        <div class="card">
            <div class="card-header">
                <h5>@lang('Anhänge')</h5>
            </div>
            <div class="card-content">
                @if(!config('app.demo'))
                    {{ Form::dropzone('attachments', __('Anhänge'), route('order.message_upload', $order)) }}
                @endif
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