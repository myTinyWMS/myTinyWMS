@extends('layout.app')

@section('content')
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Details</h5>
                </div>
                <div class="ibox-content">
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