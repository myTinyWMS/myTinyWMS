@extends('layout.app')

@section('title', 'Artikelübersicht')

@section('content')

{!! Form::open(['route' => ['article.print_label'], 'method' => 'POST', 'id' => 'print_label_form']) !!}
{!! $dataTable->table() !!}
<input type="hidden" id="label_quantity" name="label_quantity" />
{!! Form::close() !!}

<div class="toolbar-top-right-content hidden">
    <div class="dropdown-menu group">
        <div class="dropdown-menu-header">
            weitere Aktionen
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
        <div class="dropdown-menu-list">
            <a href="{{ route('article.mass_update_form') }}">Massenupdate</a>
            <a href="{{ route('article.inventory_update_form') }}">Inventurupdate</a>
            <a href="{{ route('article.sort_update_form') }}">Sortierung</a>
        </div>
    </div>
    <a href="{{ route('article.create') }}" class="btn btn-primary">Neuer Artikel</a>
</div>

<div class="toolbar_content hidden">
    <div class="dropdown-menu group">
        <div class="dropdown-menu-header">
            Aktion
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
        <div class="dropdown-menu-list">
            <a href="#" id="print_small_label">Label erstellen (klein)</a>
            <a href="#" id="print_large_label">Label erstellen (groß)</a>
        </div>
        <input type="hidden" name="label_size" id="label_size" value="" />
    </div>
</div>
@endsection

@section('datatableFilters')
    <label>
       Kategorie:&nbsp;
        <select id="filterCategory" data-target-col="{{ \Mss\DataTables\ArticleDataTable::CATEGORY_COL_ID }}" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($categories as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
        Tags:&nbsp;
        <select id="filterTags" data-target-col="{{ \Mss\DataTables\ArticleDataTable::TAGS_COL_ID }}" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($tags as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
       Lieferant:&nbsp;
        <select id="filterSupplier" data-target-col="{{ \Mss\DataTables\ArticleDataTable::SUPPLIER_COL_ID }}" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($supplier as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
       Status:&nbsp;
        <select id="filterStatus" data-target-col="{{ \Mss\DataTables\ArticleDataTable::STATUS_COL_ID }}" class="form-control input-sm datatableFilter-select" data-pre-select="1">
            <option value="all">alle</option>
            <option value="1">aktiv</option>
            <option value="0">deaktiviert</option>
            <option value="2">Bestellstopp</option>
        </select>
    </label>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function () {
            $("body").on('dt.filter.filterCategory', function () {
                if (parseInt($('#filterCategory').val()) > 0) {
                    window.LaravelDataTables.dataTableBuilder.columns(1).visible(true);
                    $('#dataTableBuilder thead th:eq(1)').click();
                } else {
                    window.LaravelDataTables.dataTableBuilder.columns(1).visible(false);
                }
            });

            $('#print_small_label').click(function () {
                $('#label_size').val('small');
                $('#label_quantity').val(window.prompt('Wieviele Label sollen gedruckt werden?', '1'));
                $('#print_label_form').submit();
                return false;
            });

            $('#print_large_label').click(function () {
                $('#label_size').val('large');
                $('#label_quantity').val(window.prompt('Wieviele Label sollen gedruckt werden?', '1'));
                $('#print_label_form').submit();
                return false;
            });

            @if(!empty($preSelectedSupplier))
                window.LaravelDataTables.dataTableBuilder.columns({{ \Mss\DataTables\ArticleDataTable::SUPPLIER_COL_ID }}).search({{ $preSelectedSupplier }}).draw();
                $('#filterSupplier option[value="{{ $preSelectedSupplier }}"]').attr('selected', 'selected');
            @endif

            @if(!empty($preSelectedCategory))
                window.LaravelDataTables.dataTableBuilder.columns({{ \Mss\DataTables\ArticleDataTable::CATEGORY_COL_ID }}).search({{ $preSelectedCategory }}).draw();
                $('#filterCategory option[value="{{ $preSelectedCategory }}"]').attr('selected', 'selected');
            @endif
        });
    </script>
@endpush