@extends('layout.app')

@section('title', __('Einstellungen'))

@section('summernote_custom_config')
    ,height: 500
@endsection

@section('content')
<form method="post" action="{{ route('settings.save') }}">
    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('Allgemein')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <div class="">
                    <label for="lang" class="form-label">@lang('Sprache')</label>
                    <select class="form-input w-32" id="lang" name="setting[{{ UserSettings::SETTINGS_LANGUAGE }}]">
                        <option value="de" {{ Auth::user()->settings()->get(UserSettings::SETTINGS_LANGUAGE) == 'de' ? 'selected="selected"' : '' }}>@lang('Deutsch')</option>
                        <option value="en" {{ Auth::user()->settings()->get(UserSettings::SETTINGS_LANGUAGE) == 'en' ? 'selected="selected"' : '' }}>@lang('Englisch')</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('Benachrichtigungen')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED)) checked @endif value="1">
                            @lang('Benachrichtigung wenn eine Lieferung zu einem Artikel erfolgt, für den bereits eine Rechnung vorhanden ist')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_WITHOUT_INVOICE_RECEIVED }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_WITHOUT_INVOICE_RECEIVED)) checked @endif value="1">
                            @lang('Benachrichtigung wenn eine Lieferung zu einem Artikel erfolgt, für den noch keine Rechnung vorhanden ist')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS)) checked @endif value="1">
                            @lang('Benachrichtigung bei Rechnungen die zur Prüfung angemerkt wurden')
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_DELIVERY_QUANTITY_DIFFERS_FROM_ORDER_QUANTITY)) checked @endif value="1">
                            @lang('Benachrichtigung bei abweichender Liefermenge')
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_ABOUT_CORRECTION_ON_CHANGE_OF_OTHER_MONTH)) checked @endif value="1">
                            @lang('Benachrichtigung bei Korrekturbuchungen zu Änderungen aus einem anderen Monat')
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="select1" class="form-label">@lang('Benachrichtigung wenn eine Lieferung zu einem Artikel der gewählten Kategorie eingeht.')</label>
                    <select class="w-1/2 select2" id="select1" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES }}][]" multiple="multiple">
                        @foreach(\Mss\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" @if(in_array($category->id, Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES))) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('Handscanner')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <div>
                    <label for="select1" class="form-label">@lang('PIN Code für Handscanner')</label>
                    <input type="text" class="form-input w-32" name="setting[{{ UserSettings::SETTINGS_HANDSCANNER_PIN_CODE }}]" value="{{ Auth::user()->settings()->get(UserSettings::SETTINGS_HANDSCANNER_PIN_CODE) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('E-Mail Einstellungen')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4 pr-2">
                {{ Form::wysiwygEditor('signature', $signature, [], __('E-Mail Signatur')) }}
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full mb-6">
        <div class="card-content flex">
            <div class="w-1/3 pt-4">
                <div class="text-lg pb-2">@lang('API Tokens')</div>
                <p class="text-sm text-gray-700 pr-20">

                </p>
            </div>
            <div class="w-2/3 pt-4">
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Berechtigungen')</th>
                            <th>@lang('Erstellt')</th>
                            <th>@lang('Zuletzt benutzt')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\Illuminate\Support\Facades\Auth::user()->tokens as $token)
                            <tr class="{{ ($loop->even) ? 'even' : ''}}">
                                <td>{{ $token->name }}</td>
                                <td>{{ implode(', ', $token->abilities) }}</td>
                                <td>{{ $token->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ optional($token->last_used_at)->format('d.m.Y H:i') }}</td>
                                <td><a href="{{ route('settings.remove_token', $token) }}">@lang('Token löschen')</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-left">@lang('Keine Tokens vorhanden')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <button type="button" @click="$modal.show('newTokenModal')" class="btn btn-secondary ml-4 mt-8">@lang('Token erstellen')</button>
            </div>
        </div>
    </div>

    <div class="card 3xl:w-2/3 w-full">
        <div class="card-content">
            @csrf
            {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary', 'id' => 'saveSettings']) !!}
        </div>
    </div>
</form>

<modal name="newTokenModal" height="auto" classes="modal" @opened="renderIChecks()">
    <h4 class="modal-title">@lang('Neuen Token erstellen')</h4>

    {!! Form::open(['route' => ['settings.create_token'], 'method' => 'POST']) !!}
    <div class="row">
        <div class="w-1/2">
            <div class="form-group">
                {{ Form::bsText('name', '', [], __('Name')) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="w-full">
            <label class="form-label">@lang('Berechtigungen')</label>
                {{ Form::bsCheckbox('abilities[]', \Mss\Models\User::API_ABILITY_ARTICLE_GET, __('Artikel abrufen')) }}
                {{ Form::bsCheckbox('abilities[]', \Mss\Models\User::API_ABILITY_ARTICLE_EDIT, __('Artikel ändern')) }}
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" @click="$modal.hide('newTokenModal')">@lang('Abbrechen')</button>
        <button type="submit" class="btn btn-primary" id="saveNewToken">@lang('Speichern')</button>
    </div>
    {!! Form::close() !!}
</modal>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush