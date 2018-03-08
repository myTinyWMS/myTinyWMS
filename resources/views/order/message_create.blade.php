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

@section('content')
{!! Form::open(['route' => ['order.store_delivery', $order], 'method' => 'POST']) !!}
<div class="row">
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Neue Nachricht an {{ $order->supplier->name }}</h5>
            </div>
            <div class="ibox-content">
                {{ Form::bsText('receiver', $order->supplier->email, [], 'Empfänger') }}
                {{ Form::bsText('subject', null, [], 'Betreff') }}
                {{ Form::summernote('body', null, [], 'Nachricht') }}
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection