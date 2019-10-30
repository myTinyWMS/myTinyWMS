@extends('layout.app')

@section('title', 'Reports')

@section('content')
<div class="px-2 -ml-2" id="reports">
    <div class="flex -mx-2 flex-wrap">
        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">Inventurauswertung</div>
                <div class="card-content">
                    <small>Monat und Inventurtyp auswählen um Report zu erstellen:</small>

                    <form method="post" action="{{ route('reports.inventory_report') }}" id="inventory-report" class="mt-4">
                        {{ csrf_field() }}
                        <div class="flex flex-wrap">
                            <div class="w-full lg:w-1/2 pr-4">
                                {{ Form::bsSelect('inventorytype', null, \Mss\Models\Article::getInventoryTextArray(),  'Inventur Typ', ['placeholder' => 'egal']) }}
                            </div>
                            <div class="w-full lg:w-1/2">
                                <label class="form-label">Monat</label>
                                <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary">Report erstellen</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">WA Vergleich</div>
                <div class="card-content">
                    <small>Monat auswählen um Report zu erstellen</small>

                    <form method="post" action="{{ route('reports.article_usage_report') }}" id="article-usage-report" class="mt-4">
                        {{ csrf_field() }}
                        <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>

                        <button type="submit" class="btn btn-secondary mt-4">Report erstellen</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">Wareneingänge mit Rechnung</div>
                <div class="card-content">
                    <small>Monat auswählen um Report zu erstellen</small>

                    <form method="post" action="{{ route('reports.invoices_with_delivery') }}" id="invoices-with-delivery" class="mt-4">
                        {{ csrf_field() }}
                        <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>

                        <button type="submit" class="btn btn-secondary mt-4">Report erstellen</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex-column">
                <div class="card mb-4 h-40">
                    <div class="card-header">Monats-Inventur-Liste</div>
                    <div class="card-content">
                        <small class="block mb-4">nur aktive Verbrauchsmaterialien</small>

                        <a class="btn btn-secondary pb-2" href="{{ route('reports.inventory_pdf') }}"><i class="fa fa-download"></i> PDF herunterladen </a>
                    </div>
                </div>

                <div class="card h-40">
                    <div class="card-header">Jahres-Inventur-Liste</div>
                    <div class="card-content relative">
                        <small class="block mb-4">alle aktiven Artikel</small>

                        <a class="btn btn-secondary pb-2" href="{{ route('reports.yearly_inventory_pdf') }}"><i class="fa fa-download"></i> PDF herunterladen </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex-column">
                <div class="card mb-4 h-40">
                    <div class="card-header">Wareneingänge ohne Rechnung</div>
                    <div class="card-content">
                        <a class="btn btn-secondary pb-2" href="{{ route('reports.deliveries_without_invoice') }}"><i class="fa fa-arrow-right"></i> Liste anzeigen </a>
                    </div>
                </div>

                <div class="card mb-4 h-40">
                    <div class="card-header">Rechnungen ohne Wareneingang</div>
                    <div class="card-content">
                        <a class="btn btn-secondary pb-2" href="{{ route('reports.invoices_without_delivery') }}"><i class="fa fa-arrow-right"></i> Liste anzeigen </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex">
                <div class="card mb-4 h-56">
                    <div class="card-header">Verpackungs-Lizensierungs-Report</div>
                    <div class="card-content">
                        <small>Zeitraum auswählen um Report zu erstellen</small>
                        <form method="post" action="{{ route('reports.article_weight_report') }}" id="article-weight-report" class="form-inline">
                            {{ csrf_field() }}

                            <date-picker-input format="DD.MM.YYYY" outputformat="YYYY-MM-DD" name="daterange" picker-class="w-auto" :default="[]"></date-picker-input>
                            <button type="submit" class="btn btn-secondary mt-4">
                                <i class="fa fa-arrow-right"></i> Liste anzeigen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection