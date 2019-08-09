@extends('layout.app')

@section('content')
    <div class="w-full flex">
        <div class="w-1/3">
            <div class="card">
                <div class="card-header">
                    <h5>Details</h5>
                </div>
                <div class="card-content">
                    @yield('form_start')

                    {{ Form::bsText('name', null, [], 'Name') }}
                    {{ Form::bsText('email', null, [], 'E-Mail') }}
                    {{ Form::bsText('phone', null, [], 'Telefon') }}
                    {{ Form::bsText('contact_person', null, [], 'Kontaktperson') }}
                    {{ Form::bsText('website', null, [], 'Webseite') }}
                    {{ Form::bsText('accounts_payable_number', null, [], 'Kreditorennummer') }}
                    {{ Form::bsTextarea('notes', null, [], 'Bemerkungen') }}

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