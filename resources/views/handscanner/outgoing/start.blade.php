@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Warenausgang - Artikel scannen</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h5 class="text-center mb-4 mt-5">Bitte einen Artikel scannen</h5>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var scanned = '';
        $(document).ready(function () {
            window.addEventListener('keypress', function(event) {
                if (event.keyCode == 13) {
                    window.location.href = '{{ route('handscanner.outgoing.process', ['']) }}/' + scanned;
                } else {
                    scanned += String.fromCharCode(event.charCode);
                }
            });
        });
    </script>
@endpush