@extends('layout.app')

@section('title', 'Inventur')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Offene Inventuren</h5>

                    <div class="pull-right">
                        <a href="{{ route('inventory.create_month') }}" class="btn btn-xs btn-primary">Neue Monats-Inventur starten</a>
                        <a href="{{ route('inventory.create_year') }}" class="btn btn-xs btn-primary">Neue Jahres-Inventur starten</a>
                    </div>
                </div>
                <div class="ibox-content">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Abgeschlossene Inventuren</h5>
                </div>
                <div class="ibox-content">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Gestartet</th>
                                <th>Abgeschlossen</th>
                                <th>Artikel</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($closedInventories as $inventory)
                            <tr>
                                <td>{{ $inventory->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $inventory->items->max('processed_at')->format('d.m.Y H:i') }}</td>
                                <td>{{ $inventory->items->count() }}</td>
                                <td>
                                    <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-primary btn-xs">Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush