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
        <div class="w-full">
            <div class="card w-1/2">
                <div class="card-header">
                    <h5>Neuer Wareneingang</h5>
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="w-1/2">
                            {{ Form::bsText('delivery_note_number', null, [], 'Lieferscheinnummer') }}
                        </div>

                        <div class="w-1/2 pl-6">
                            {{ Form::bsText('delivery_date', \Carbon\Carbon::now()->format('d.m.Y'), ['class' => 'form-control datepicker', 'data-date-end-date' => '0d'], 'Lieferdatum') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-full">
                            {{ Form::bsTextarea('notes', null, ['rows' => 3], 'Bemerkungen') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Gelieferte Artikel</h5>
                </div>
                <div class="card-content">
                    @foreach($order->items as $key => $item)
                        <div class="rounded border border-blue-700 p-4 mb-4 row">
                            <div class="w-4/12">
                                <small class="form-label">Artikel {{ $key+1 }}</small>
                                <h4>
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                    <br/>
                                    <small class="p-t-8"># {{ $item->article->article_number }}</small>
                                </h4>
                            </div>
                            <div class="w-2/12">
                                <small class="form-label">Bestellnummer</small>
                                <h4>{{ $item->article->currentSupplierArticle->order_number }}</h4>
                            </div>
                            <div class="w-1/12">
                                <small class="form-label">bestellte Menge</small>
                                <h4>{{ $item->quantity }}</h4>
                            </div>
                            <div class="w-1/12">
                                <small class="form-label">bereits geliefert</small>
                                <h4>{{ $item->getQuantityDelivered() }}</h4>
                            </div>
                            <div class="w-2/12 mr-2">
                                <small class="form-label">gelieferte Menge</small>
                                <div class="mt-2 flex">
                                    <input class="form-input mr-2" type="text" name="quantities[{{ $item->article->id }}]">
                                    <div class="">
                                        <button type="button" class="btn btn-success set-full-quantity" data-quantity="{{ ($item->quantity - $item->getQuantityDelivered() > 0) ? ($item->quantity - $item->getQuantityDelivered()) : 0 }}" title="alles"><i class="fa fa-check"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="w-2/12 mr-2">
                                <small class="form-label">Label drucken</small>
                                <div class="row mt-2">
                                    <div class="w-4/12 mr-2">
                                        <div class="input-group">
                                            <input class="form-input" type="text" name="label_count[{{ $item->article->id }}]" value="0">
                                        </div>
                                    </div>
                                    <div class="w-8/12">
                                        <div class="input-group has-feedback">
                                            <select class="form-select pl-2" name="label_type[{{ $item->article->id }}]">
                                                <option value="small">Klein</option>
                                                <option value="large">Groß</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-content">
                    <div class="form-group">
                        {!! Form::submit('Speichern', ['class' => 'btn btn-primary force-inline', 'id' => 'save-delivery']) !!}
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
