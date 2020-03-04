@extends('layout.app')

@section('content')
    <div class="w-full flex">
        <div class="w-1/3">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Details')</h5>
                </div>
                <div class="card-content">
                    @yield('form_start')

                    {{ Form::bsText('name', null, [], __('Name')) }}
                    {{ Form::bsText('email', null, [], __('E-Mail')) }}
                    {{ Form::bsText('phone', null, [], __('Telefon')) }}
                    {{ Form::bsText('contact_person', null, [], __('Kontaktperson')) }}
                    {{ Form::bsText('website', null, [], __('Webseite')) }}
                    {{ Form::bsText('accounts_payable_number', null, [], __('Kreditorennummer')) }}
                    {{ Form::bsTextarea('notes', null, [], __('Bemerkungen')) }}

                    <div class="form-group">
                        @yield('submit')
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
@endsection