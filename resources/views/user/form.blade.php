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
                    {{ Form::bsText('username', null, [], __('Benutzername')) }}

                    <div class="form-group">
                        @yield('submit')
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="w-1/3 ml-4">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Rollen')</h5>
                </div>
                <div class="card-content">
                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                        {{ Form::bsCheckbox('role_'.$role->id, $role->id, \Illuminate\Support\Facades\Auth::user()->hasRole($role), [], []) }}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection