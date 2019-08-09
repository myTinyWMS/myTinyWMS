@extends('layout.app')

@section('title', 'Aktive Inventuren')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')

    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('inventory.create_month') }}" class="btn btn-primary mr-4">Neue Monats-Inventur starten</a>
        <a href="{{ route('inventory.create_year') }}" class="btn btn-primary">Neue Jahres-Inventur starten</a>
    </div>

    {!! $dataTable->table() !!}

    <div class="mt-8">
        <h1>Abgeschlossene Inventuren</h1>

        <div class="table-wrapper">
            <table class="table dataTable">
                <thead>
                <tr>
                    <th>Gestartet</th>
                    <th>Abgeschlossen</th>
                    <th>Artikel</th>
                    <th>Aktion</th>
                </tr>
                </thead>
                <tbody>
                @foreach($closedInventories as $inventory)
                    <tr>
                        <td>{{ $inventory->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $inventory->items->max('processed_at')->format('d.m.Y H:i') }}</td>
                        <td>{{ $inventory->items->count() }}</td>
                        <td>
                            <a href="{{ route('inventory.show', $inventory) }}" class="table-action">Details</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush