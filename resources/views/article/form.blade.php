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
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Details</h5>
                </div>
                <div class="ibox-content">
                    @yield('form_start')

                    {{ Form::bsTextarea('name', null, ['rows' => 2] , 'Name') }}
                    {{ Form::bsSelect('unit', $article->unit_id, \Mss\Models\Unit::all()->pluck('name', 'id'),  'Einheit') }}
                    {{ Form::bsText('sort_id', null, [], 'Sortierung') }}
                    {{ Form::bsText('quantity', null, [], 'Bestand') }}
                    {{ Form::bsText('min_quantity', null, [], 'Mindestbestand') }}
                    {{ Form::bsText('usage_quantity', null, [], 'Verbrauch') }}
                    {{ Form::bsText('issue_quantity', null, [], 'Entnahmemenge') }}
                    {{ Form::bsCheckbox('inventory', null, 'Inventur', $article->inventory, []) }}
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