@extends('layout.app')

@section('title', __('Aktive Inventuren'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Übersicht')</strong>
    </li>
@endsection

@section('content')

    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('inventory.create_month') }}" class="btn btn-secondary mr-4">@lang('Neue Monats-Inventur starten')</a>
        <a href="{{ route('inventory.create_year') }}" class="btn btn-secondary">@lang('Neue Jahres-Inventur starten')</a>
    </div>

    {!! $dataTable->table() !!}

    <div class="mt-8">
        <h1>@lang('Abgeschlossene Inventuren')</h1>

        <div class="table-wrapper">
            <table class="table dataTable">
                <thead>
                <tr>
                    <th>@lang('Gestartet')</th>
                    <th>@lang('Abgeschlossen')</th>
                    <th>@lang('Artikel')</th>
                    <th>@lang('Aktion')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($closedInventories as $inventory)
                    <tr>
                        <td>{{ $inventory->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $inventory->items->max('processed_at')->format('d.m.Y H:i') }}</td>
                        <td>{{ $inventory->items->count() }}</td>
                        <td>
                            <a href="{{ route('inventory.show', $inventory) }}" class="table-action">@lang('Details')</a>
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