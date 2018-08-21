@extends('layout.app')

@section('title', 'Artikelübersicht')

@section('content')
<div class="row">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Artikelübersicht</h5>
                    <div class="pull-right">
                        <a href="{{ route('article.create') }}" class="btn btn-primary btn-xs">Neuer Artikel</a>
                        <div class="btn-group">
                            <button data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle" aria-expanded="true">weitere Aktionen <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('article.mass_update_form') }}">Massenupdate</a></li>
                                <li><a href="{{ route('article.inventory_update_form') }}">Inventurupdate</a></li>
                                <li><a href="{{ route('article.sort_update_form') }}">Sortierung</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="ibox-content">
                    {!! Form::open(['route' => ['article.print_label'], 'method' => 'POST', 'id' => 'print_label_form']) !!}
                    {!! $dataTable->table() !!}
                    <input type="hidden" id="label_quantity" name="label_quantity" />
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="toolbar_content hidden">
    <div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Aktion <span class="caret"></span></button>
        <ul class="dropdown-menu">
            <li><a href="#" id="print_small_label">Label erstellen (klein)</a></li>
            <li><a href="#" id="print_large_label">Label erstellen (groß)</a></li>
        </ul>
    </div>
    <input type="hidden" name="label_size" id="label_size" value="" />
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
                } else {
                    window.LaravelDataTables.dataTableBuilder.columns(1).visible(false);
                }
            });

            $('.toolbar').html($('.toolbar_content').html());

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
        });
    </script>
@endpush