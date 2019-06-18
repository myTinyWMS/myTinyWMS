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
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <div class="card">
            <div class="card-header">Lieferant</div>
            <div class="card-content">
                {{ Form::bsSelect('supplier_id', null, \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'),  'Lieferant', ['placeholder' => '', 'required' => 'required']) }}
                {{ Form::bsText('supplier_order_number', null, [], 'Bestellnummer') }}
                {{ Form::bsText('supplier_price', null, [], 'Preis netto') }}
                {{ Form::bsText('supplier_delivery_time', null, [], 'Lieferzeit (Wochentage)') }}
                {{ Form::bsText('supplier_order_quantity', null, [], 'Bestellmenge') }}
            </div>
        </div>
    </div>
@endsection