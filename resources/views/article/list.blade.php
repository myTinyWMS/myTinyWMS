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
                    </div>
                </div>
                <div class="ibox-content">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('datatableFilters')
    <label>
       Kategorie:&nbsp;
        <select id="filterCategory" data-target-col="13" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($categories as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
        Tags:&nbsp;
        <select id="filterTags" data-target-col="12" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($tags as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
       Lieferant:&nbsp;
        <select id="filterSupplier" data-target-col="11" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($supplier as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>
    <label>
       Status:&nbsp;
        <select id="filterStatus" data-target-col="14" class="form-control input-sm datatableFilter-select" data-pre-filter="1">
            <option value="1">aktiv</option>
            <option value="0">deaktiviert</option>
        </select>
    </label>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        window.LaravelDataTables.dataTableBuilder.on( 'row-reorder', function ( e, diff, edit ) {
            var myArray = [];
            for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
                var rowData = window.LaravelDataTables.dataTableBuilder.row( diff[i].node ).data();
                myArray.push({
                    id: rowData.id,			// record id from datatable
                    position: diff[i].newPosition		// new position
                });
            }
            var jsonString = JSON.stringify(myArray);
            $.ajax({
                url     : '{{ URL::to('article/reorder') }}',
                type    : 'POST',
                data    : jsonString,
                dataType: 'json',
                success : function ( json )
                {
                    $('#dataTableBuilder').DataTable().ajax.reload(); // now refresh datatable
                    $.each(json, function (key, msg) {
                        // handle json response
                    });
                }
            });
        });
    </script>
@endpush