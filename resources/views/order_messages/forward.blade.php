@extends('layout.app')

@section('title', __('Nachricht weiterleiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Nachricht weiterleiten')</strong>
    </li>
@endsection

@section('summernote_show_signature_button', true)
@section('summernote_signature', html_entity_decode(Auth::user()->signature))

@section('content')
<div class="row">
    <div class="w-8/12 mr-4">
        {!! Form::open(['route' => ['order.message_forward', $message], 'method' => 'POST', 'id' => 'forwardMessageForm']) !!}
        <div class="card">
            <div class="card-header">
                <h5>@lang('Nachricht weiterleiten') </h5>
            </div>
            <div class="card-content">
                {{ Form::bsText('receiver', $preSetReceiver, [], __('Empfänger (mehrere mit Komma getrennt)')) }}
                {{ Form::bsText('subject', $preSetSubject, [], __('Betreff')) }}

                @if (!empty($message->htmlBody))
                    {{ Form::wysiwygEditor('content', $message->htmlBody, [], __('Nachricht')) }}
                @else
                    {{ Form::wysiwygEditor('content', nl2br($message->textBody), [], __('Nachricht')) }}
                @endif

                <hr class="hr-line-solid">
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