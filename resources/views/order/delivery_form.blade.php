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
        <div class="col-lg-12 col-xxl-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Neuer Wareneingang</h5>
                </div>
                <div class="ibox-content">
                    {{ Form::bsText('delivery_note_number', null, [], 'Lieferscheinnummer') }}
                    {{ Form::bsText('delivery_date', \Carbon\Carbon::now()->format('d.m.Y'), ['class' => 'form-control datepicker', 'data-date-end-date' => '0d'], 'Lieferdatum') }}
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
                                <div class="col-lg-6">
                                    <small class="stats-label">Artikel</small>
                                    <h4>
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                        <br/>
                                        <small class="p-t-8"># {{ $item->article->article_number }}</small>
                                    </h4>
                                </div>
                                <div class="col-lg-2">
                                    <small class="stats-label">bestellte Menge</small>
                                    <h4>{{ $item->quantity }}</h4>
                                </div>
                                <div class="col-lg-2">
                                    <small class="stats-label">bereits geliefert</small>
                                    <h4>{{ $item->getQuantityDelivered() }}</h4>
                                </div>
                                <div class="col-lg-2">
                                    <small class="stats-label">gelieferte Menge</small>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="quantities[{{ $item->article->id }}]">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-success set-full-quantity" data-quantity="{{ ($item->quantity - $item->getQuantityDelivered() > 0) ? ($item->quantity - $item->getQuantityDelivered()) : 0 }}" title="alles"><i class="fa fa-check"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="form-group">
                        {!! Form::submit('Speichern', ['class' => 'btn btn-primary force-inline']) !!}
                        {{ Form::bsCheckbox('print_label', 1, 'Label drucken', false, [], 'force-inline p-xl') }}
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
            $('.set-full-quantity').click(function () {
                $(this).parent().parent().find('input').val($(this).attr('data-quantity'));
            });

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
