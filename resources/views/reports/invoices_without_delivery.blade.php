@extends('layout.app')

@section('title', __('Rechnungen ohne Wareneingang'))

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">@lang('Reports')</a>
    </li>
    <li class="active">
        <strong>@lang('Rechnungen ohne Wareneingang')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-content">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Artikel')</th>
                                    <th>@lang('Bestellung')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($openItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('order.show', $item->order) }}" target="_blank">{{ $item->order->internal_order_number }}</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection