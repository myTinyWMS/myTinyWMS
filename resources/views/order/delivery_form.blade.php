@extends('layout.app')

@section('title', 'Neuer Wareneingang')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li>
        <a href="{{ route('order.show', $order) }}">Bestellung #{{ $order->internal_order_number }}</a>
    </li>
    <li class="active">
        <strong>Neuer Wareneingang</strong>
    </li>
@endsection

@section('content')
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {!! Form::open(['route' => ['order.store_delivery', $order], 'method' => 'POST']) !!}
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Neuer Wareneingang</h5>
                </div>
                <div class="ibox-content">
                    {{ Form::bsText('delivery_note_number', null, [], 'Lieferscheinnummer') }}
                    {{ Form::bsText('delivery_date', '', ['class' => 'form-control datepicker', 'data-date-end-date' => '0d'], 'Lieferdatum') }}
                    {{ Form::bsTextarea('notes', null, [], 'Bemerkungen') }}
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Gelieferte Artikel</h5>
                </div>
                <div class="ibox-content">
                    @foreach($order->items as $item)
                        <div class="panel panel-primary">
                            <div class="panel-body row">
                                <div class="col-lg-7">
                                    <small class="stats-label">Artikel</small>
                                    <h4>{{ $item->article->name }}</h4>
                                </div>
                                <div class="col-lg-2">
                                    <small class="stats-label">bestellte Menge</small>
                                    <h4>{{ $item->quantity }}</h4>
                                </div>
                                <div class="col-lg-3">
                                    <small class="stats-label">gelieferte Menge</small>
                                    {{ Form::bsText('quantities['.$item->article->id.']', null, [], '') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="form-group">
                        {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
    {!! Form::close() !!}
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.datepicker').datepicker({
                format: 'dd.mm.yyyy',
                language: 'de',
                todayHighlight: true,
                daysOfWeekDisabled: [0,6],
                autoclose: true
            });
        })
    </script>
@endpush
