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

@section('summernote_show_signature_button', true)
@section('summernote_signature', html_entity_decode(Auth::user()->signature))

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

                @if (!empty($message->htmlBody))
                    {{ Form::summernote('content', $message->htmlBody, [], 'Nachricht') }}
                @else
                    {{ Form::summernote('content', nl2br($message->textBody), [], 'Nachricht') }}
                @endif

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