@extends('layout.app')

@section('title', __('Einstellungen'))

@section('content')

<form method="post" action="{{ route('admin.settings.save') }}">
    <div class="w-full">
        <div class="card 2xl:w-2/3 w-full mb-6">
            <div class="card-content flex">
                <div class="w-1/2 pt-4">
                    <div class="text-lg pb-2">@lang('Ausgehende E-Mails')</div>
                    <p class="text-sm text-gray-700 pr-20">
                        @lang('Definieren Sie hier den SMTP Server der für ausgehende E-Mails verwendet werden soll.')
                    </p>
                </div>
                <div class="w-1/2 pt-4">
                    <div class="row">
                        <div class="w-2/3 pr-4">
                            {{ Form::bsText('smtp_host', settings('smtp.host'), [], __('Host')) }}
                        </div>
                        <div class="w-1/3 pr-4">
                            {{ Form::bsText('smtp_port', settings('smtp.port'), [], __('Port')) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('smtp_username', !empty(settings('smtp.username')) ? decrypt(settings('smtp.username')) : '', [], __('Username')) }}
                        </div>
                        <div class="w-1/2 pr-4">
                            <password-field value="{{ old('smtp_password', !empty(settings('smtp.password')) ? decrypt(settings('smtp.password')) : '') }}" id="smtp_password" name="smtp_password" label="@lang('Passwort')"></password-field>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsSelect('smtp_encryption', settings('smtp.encryption'), ['tls' => __('TLS'), 'ssl' => __('SSL')],  __('Verschlüsselung'), ['placeholder' => __('Keine')]) }}
                        </div>
                        <div class="w-1/2 pr-4">

                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('smtp_from_address', settings('smtp.from_address'), [], __('Absender-Adresse')) }}
                        </div>
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('smtp_from_name', settings('smtp.from_name'), [], __('Absender-Name')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card 2xl:w-2/3 w-full mb-6">
            <div class="card-content flex">
                <div class="w-1/2 pt-4">
                    <div class="text-lg pb-2">@lang('Import eingehender E-Mails für Bestellungen')</div>
                    <p class="text-sm text-gray-700 pr-20">
                        @lang('myTinyWMS kann eingehende E-Mails automatisch importieren und Bestellungen zuweisen. Wenn Sie dieses Feature nutzen wollen, geben Sie bitte nachfolgende die IMAP Zugangsdaten ein.')
                    </p>
                </div>
                <div class="w-1/2 pt-4">
                    <div class="row">
                        <div class="w-full pb-4">
                            {{ Form::bsCheckbox('imap_enabled', settings('imap.enabled', false), __('E-Mails automatisch importieren')) }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsText('imap_username', !empty(settings('imap.username')) ? decrypt(settings('imap.username')) : '', [], __('Username')) }}
                        </div>
                        <div class="w-1/2 pr-4">
                            <password-field value="{{ old('imap_password', !empty(settings('imap.password')) ? decrypt(settings('imap.password')) : '') }}" id="imap_password" name="imap_password" label="@lang('Passwort')"></password-field>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2 pr-4">
                            {{ Form::bsSelect('imap_encryption', settings('imap.encryption'), ['tls' => __('TLS'), 'ssl' => __('SSL')],  __('Verschlüsselung'), ['placeholder' => __('Keine')]) }}
                        </div>
                        <div class="w-1/2 pr-4">

                        </div>
                    </div>
                    <div class="row">
                        <div class="w-full pb-4">
                            {{ Form::bsCheckbox('imap_delete', settings('imap.delete', false), __('E-Mails nach dem Import vom Server löschen')) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card 2xl:w-2/3 w-full">
        <div class="card-content">
            @csrf
            {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary', 'id' => 'saveSettings']) !!}
        </div>
    </div>
</form>
@endsection