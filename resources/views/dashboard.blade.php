@extends('layout.app')

@section('title', 'Dashboard')

@section('content')

{!! Form::open(['route' => ['order.create_post'], 'method' => 'POST']) !!}
{!! $dataTable->table() !!}
{!! Form::close() !!}

<div class="footer_actions hidden">
    <button class="btn btn-xs btn-secondary" type="submit" id="create_new_order">Bestellung erstellen</button>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        var currentlySelectedSupplier = null;
        $(document).ready(function () {
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