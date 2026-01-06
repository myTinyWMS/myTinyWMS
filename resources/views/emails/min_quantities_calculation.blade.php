@component('mail::message')

@component('mail::panel')
@lang('Sehr geehrte Damen und Herren,')

@lang('für folgende Artikel gibt es neue Empfehlungen für Mindestbestände:')


@component('mail::table')
| # | @lang('Artikel') | @lang('aktueller Mindestbestand') | @lang('empfohlener Mindestbestand') |
| :--- | :--- | :---: | :---: |
@foreach($items as $key => $item)
@if(!is_null($item['new_quantity']))
| {{ $key + 1 }} | {{ $item['article']->name }} | {{ $item['article']->min_quantity }} | {{ $item['new_quantity'] }} |
@endif
@endforeach
@endcomponent

@endcomponent