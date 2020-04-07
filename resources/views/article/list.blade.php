@extends('layout.app')

@section('title', __('Artikelübersicht'))

@section('content')

<div class="table-toolbar-right-content hidden">
    @can('article.edit')
        <div class="dropdown-list group">
            <div class="flex items-center cursor-pointer text-sm text-blue-500 rounded-t-lg py-1 px-2">
                @lang('weitere Aktionen')
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
            </div>
            <div class="dropdown-list-items">
                <a href="{{ route('article.mass_update_form') }}">@lang('Massenupdate')</a>
                <a href="{{ route('article.inventory_update_form') }}">@lang('Inventurupdate')</a>
            </div>
        </div>
    @endcan

    @can('article-group.view')
        <a href="{{ route('article-group.index') }}" class="btn btn-secondary mr-2">@lang('Artikelgruppen verwalten')</a>
    @endcan

    @can('article.create')
        <a href="{{ route('article.create') }}" class="btn btn-secondary">@lang('Neuer Artikel')</a>
    @endcan
</div>

{!! Form::open(['route' => ['article.print_label'], 'method' => 'POST', 'id' => 'print_label_form']) !!}
{!! $dataTable->table() !!}
<input type="hidden" id="label_quantity" name="label_quantity" />
{!! Form::close() !!}

<div class="footer_actions hidden">
    <div class="dropdown-list group">
        <div class="flex items-center cursor-pointer text-sm text-blue-500 rounded-t-lg py-1 px-2">
            @lang('Aktion')
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
        </div>
        <div class="dropdown-list-items">
            <a href="#" id="print_small_label">@lang('Label erstellen (klein)')</a>
            <a href="#" id="print_large_label">@lang('Label erstellen (groß)')</a>
        </div>
        <input type="hidden" name="label_size" id="label_size" value="" />
    </div>
</div>

<data-tables-filter>
    <data-tables-filter-select is-article-category-col="true" label="Kategorie" col-id="{{ \Mss\DataTables\ArticleDataTable::CATEGORY_COL_ID }}" id="filterCategory">
        <option value=""></option>
        @foreach($categories as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </data-tables-filter-select>

    <data-tables-filter-select label="Tags" col-id="{{ \Mss\DataTables\ArticleDataTable::TAGS_COL_ID }}">
        <option value=""></option>
        @foreach($tags as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </data-tables-filter-select>

    <data-tables-filter-select label="Lieferant" col-id="{{ \Mss\DataTables\ArticleDataTable::SUPPLIER_COL_ID }}" id="filterSupplier">
        <option value=""></option>
        @foreach($supplier as $item)
            <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
    </data-tables-filter-select>

    <data-tables-filter-select label="Status" pre-set="1" col-id="{{ \Mss\DataTables\ArticleDataTable::STATUS_COL_ID }}" id="filterStatus">
        <option value="all">@lang('alle')</option>
        <option value="1">@lang('aktiv')</option>
        <option value="0">@lang('deaktiviert')</option>
        <option value="2">@lang('Bestellstopp')</option>
    </data-tables-filter-select>
</data-tables-filter-select>
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

            $( "#dataTableBuilder tbody" ).on( "click", "tr", function() {
                $(this).find('td:eq(0) .iCheck-helper').click();
            });

            $('#print_small_label').click(function () {
                $('#label_size').val('small');
                $('#label_quantity').val(window.prompt('@lang('Wieviele Label sollen gedruckt werden?')', '1'));
                $('#print_label_form').submit();
                return false;
            });

            $('#print_large_label').click(function () {
                $('#label_size').val('large');
                $('#label_quantity').val(window.prompt('@lang('Wieviele Label sollen gedruckt werden?')', '1'));
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