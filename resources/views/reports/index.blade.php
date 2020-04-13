@extends('layout.app')

@section('title', __('Reports'))

@section('content')
<div class="px-2 -ml-2" id="reports">
    <div class="flex -mx-2 flex-wrap">
        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('Inventurauswertung')</div>
                <div class="card-content">
                    <small>@lang('Monat und Inventurtyp auswählen um Report zu erstellen:')</small>

                    <form method="post" action="{{ route('reports.inventory_report') }}" id="inventory-report" class="mt-4">
                        {{ csrf_field() }}
                        <div class="flex flex-wrap">
                            <div class="w-full lg:w-1/2 pr-4">
                                {{ Form::bsSelect('inventorytype', null, \Mss\Models\Article::getInventoryTextArray(),  __('Inventur Typ'), ['placeholder' => __('egal')]) }}
                            </div>
                            <div class="w-full lg:w-1/2">
                                <label class="form-label">@lang('Monat')</label>
                                <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary">@lang('Report erstellen')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('WA Vergleich')</div>
                <div class="card-content">
                    <small>@lang('Monat auswählen um Report zu erstellen')</small>

                    <form method="post" action="{{ route('reports.article_usage_report') }}" id="article-usage-report" class="mt-4">
                        {{ csrf_field() }}
                        <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>

                        <button type="submit" class="btn btn-secondary mt-4">@lang('Report erstellen')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('Wareneingänge mit Rechnung')</div>
                <div class="card-content">
                    <small>@lang('Monat auswählen um Report zu erstellen')</small>

                    <form method="post" action="{{ route('reports.invoices_with_delivery') }}" id="invoices-with-delivery" class="mt-4">
                        {{ csrf_field() }}
                        <div class="flex flex-wrap">
                            <div class="w-full lg:w-1/2 pr-4">
                                {{ Form::bsSelect('category', null, \Mss\Models\Category::orderBy('name')->pluck('name', 'id'),  __('Kategorie'), ['placeholder' => 'alle']) }}
                            </div>
                            <div class="w-full lg:w-1/2">
                                <label class="form-label">@lang('Monat')</label>
                                <date-picker-input format="YYYY-MM" outputformat="YYYY-MM" type="inline" name="month" picker-class="w-auto"></date-picker-input>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-secondary mt-4">@lang('Report erstellen')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2 mb-4">
            <div class="card h-full">
                <div class="card-header">@lang('Lagerliste drucken')</div>
                <div class="card-content">
                    <form method="post" action="{{ route('reports.print_category_list') }}" class="form-inline">
                        @csrf
                        {{ Form::bsSelect('category[]', null, \Mss\Models\Category::orderBy('name')->pluck('name', 'id'),  __('Kategorien auswählen'), ['multiple', 'size' => 12, 'class' => 'form-select h-auto']) }}
                        <div class="text-gray-600 text-xs mb-6">@lang('Mehrfachwauswahl durch gedrückt halten von Strg (Mac: Cmd)')</div>
                        <button type="submit" class="btn btn-secondary pb-2"><i class="fa fa-download"></i> @lang('PDF herunterladen') </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex-column">
                <div class="card mb-4 h-40">
                    <div class="card-header">@lang('Monats-Inventur-Liste')</div>
                    <div class="card-content">
                        <small class="block mb-4">@lang('nur aktive Verbrauchsmaterialien')</small>

                        <a class="btn btn-secondary pb-2" href="{{ route('reports.inventory_pdf') }}"><i class="fa fa-download"></i> @lang('PDF herunterladen') </a>
                    </div>
                </div>

                <div class="card h-40">
                    <div class="card-header">@lang('Jahres-Inventur-Liste')</div>
                    <div class="card-content relative">
                        <small class="block mb-4">@lang('alle aktiven Artikel')</small>

                        <a class="btn btn-secondary pb-2" href="{{ route('reports.yearly_inventory_pdf') }}"><i class="fa fa-download"></i> @lang('PDF herunterladen') </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex-column">
                <div class="card mb-4 h-40">
                    <div class="card-header">@lang('Wareneingänge ohne Rechnung')</div>
                    <div class="card-content">
                        <a class="btn btn-secondary pb-2" href="{{ route('reports.deliveries_without_invoice') }}"><i class="fa fa-arrow-right"></i> @lang('Liste anzeigen') </a>
                    </div>
                </div>

                <div class="card mb-4 h-40">
                    <div class="card-header">@lang('Rechnungen ohne Wareneingang')</div>
                    <div class="card-content">
                        <a class="btn btn-secondary pb-2" href="{{ route('reports.invoices_without_delivery') }}"><i class="fa fa-arrow-right"></i> @lang('Liste anzeigen') </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-1/2 xl:w-1/3 2xl:w-1/4 4xl:w-1/5 px-2">
            <div class="flex">
                <div class="card mb-4 h-56">
                    <div class="card-header">@lang('Verpackungs-Lizensierungs-Report')</div>
                    <div class="card-content">
                        <small>@lang('Zeitraum auswählen um Report zu erstellen')</small>
                        <form method="post" action="{{ route('reports.article_weight_report') }}" id="article-weight-report" class="form-inline">
                            @csrf

                            <date-picker-input format="DD.MM.YYYY" outputformat="YYYY-MM-DD" name="daterange" picker-class="w-auto" :default="[]"></date-picker-input>
                            <button type="submit" class="btn btn-secondary mt-4">
                                <i class="fa fa-arrow-right"></i> @lang('Liste anzeigen')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection