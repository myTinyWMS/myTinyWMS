@extends('article.form')

@section('title', 'Neuer Artikel')

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Neuer Artikel</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($article, ['route' => ['article.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection

@section('secondCol')
    <div class="col-lg-6 col-xxl-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Lieferant</h5>
            </div>
            <div class="ibox-content">
                {{ Form::bsSelect('supplier_id', null, \Mss\Models\Supplier::pluck('name', 'id'),  'Lieferant', ['placeholder' => '', 'required' => 'required']) }}
                {{ Form::bsText('supplier_order_number', null, [], 'Bestellnummer') }}
                {{ Form::bsText('supplier_price', null, [], 'Preis') }}
                {{ Form::bsText('supplier_delivery_time', null, [], 'Lieferzeit') }}
                {{ Form::bsText('supplier_order_quantity', null, [], 'Bestellmenge') }}
            </div>
        </div>
    </div>
@endsection