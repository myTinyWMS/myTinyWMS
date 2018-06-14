@extends('layout.app')

@section('title', 'Einstellungen')

@section('summernote_custom_config')
    ,height: 500
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-content">
                    <form method="post" action="{{ route('settings.save') }}">
                        <div class="row">
                            <div class="m-b-lg">
                                <div class="i-checks">
                                    <label>
                                        <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IF_INVOICE_RECEIVED)) checked @endif value="1">
                                        Benachrichtigung wenn eine Lieferung zu einem Artikel erfolgt, für den bereits eine Rechnung vorhanden ist
                                    </label>
                                </div>
                            </div>

                            <div class="m-b-lg">
                                <div class="i-checks">
                                    <label>
                                        <input type="checkbox" name="setting[{{ UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS }}]" @if(Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_ON_INVOICE_CHECKS)) checked @endif value="1">
                                        Benachrichtigung bei Rechnungen die zur Prüfung angemerkt wurden
                                    </label>
                                </div>
                            </div>

                            <div class="m-b-lg">
                                <label for="select1">Benachrichtigung wenn eine Lieferung zu einem Artikel der gewählten Kategorie eingeht.</label>
                                <select class=" col-lg-6 select2" id="select1" name="setting[{{ UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES }}][]" multiple="multiple">
                                    @foreach(\Mss\Models\Category::all() as $category)
                                        <option value="{{ $category->id }}" @if(in_array($category->id, Auth::user()->settings()->get(UserSettings::SETTING_NOTIFY_AFTER_NEW_DELIVERY_IN_THOSE_CATEGORIES))) selected @endif>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="m-b-lg">
                                {{ Form::summernote('signature', $signature, [], 'E-Mail Signatur') }}
                            </div>

                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush