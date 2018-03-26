@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>zu Bestellen - Artikel die den Mindestbestand erreicht oder unterschritten haben</h5>
                </div>
                <div class="ibox-content">
                    {!! Form::open(['route' => ['order.create_post'], 'method' => 'POST']) !!}
                    {!! $dataTable->table() !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toolbar_content hidden">
    <button class="btn btn-xs btn-primary" type="submit">Bestellung erstellen</button>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        var currentlySelectedSupplier = null;
        $(document).ready(function () {
            $('.toolbar').html($('.toolbar_content').html());

            $('.dataTable').on("click", 'input[type="checkbox"]', function () {
                if ($('input[name="article[]"]:checked').length === 0) {
                    currentlySelectedSupplier = null;
                    $('.dataTable input[type="checkbox"]').attr('disabled', false).attr('title', '');
                } else {
                    currentlySelectedSupplier = $(this).parent().parent().attr('data-supplier');
                    $('.dataTable tbody tr[data-supplier!=' + currentlySelectedSupplier + '] input[type="checkbox"]').attr('disabled', true).attr('title', 'Es können nur Artikel eines Herstellers ausgewählt werden');
                }
            });
        });
    </script>
@endpush