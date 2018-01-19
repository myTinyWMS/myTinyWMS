@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Artikelübersicht</h5>
                </div>
                <div class="ibox-content">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        window.LaravelDataTables['dataTableBuilder'].on( 'row-reorder', function ( e, diff, edit ) {
            console.log(diff);
            var myArray = [];
            for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
                var rowData = window.LaravelDataTables['dataTableBuilder'].row( diff[i].node ).data();
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