@component('mail::message')

@component('mail::panel')
@lang('Sehr geehrte Damen und Herren,')

@lang('folgende Artikel haben den Mindestbestand unterschritten:')


@component('mail::table')
| # | @lang('Artikel') | @lang('aktueller Bestand') | @lang('Mindestbestand') |
| :--- | :--- | :---: | :---: |
@foreach($items as $key => $article)
| {{ $key + 1 }} | {{ $article->name }} | {{ $article->quantity }} | {{ $article->min_quantity }} |
@endforeach
@endcomponent


@component('mail::button', ['url' => route('dashboard'), 'color' => 'primary'])
@lang('Dashboard anzeigen')
@endcomponent

@endcomponent