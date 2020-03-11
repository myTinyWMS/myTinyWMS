@extends('layout.app')

@section('content')
    @yield('form_start')
    <div class="w-full flex">
        <div class="w-1/3">
            <div class="card">
                <div class="card-header flex">
                    <h5 class="flex-1">@lang('Details')</h5>

                    <div class="badge badge-default">{{ $user->getSource() }}</div>
                </div>
                <div class="card-content">
                    {{ Form::bsText('name', null, [], __('Name')) }}
                    {{ Form::bsText('email', null, ($user->getSource() == \Mss\Models\User::SOURCE_LDAP ? ['disabled' => 'disabled'] : []), __('E-Mail')) }}
                    {{ Form::bsText('username', null, ($user->getSource() == \Mss\Models\User::SOURCE_LDAP ? ['disabled' => 'disabled'] : []), __('Benutzername')) }}
                    {{ Form::bsPassword('password', [], __('Passwort')) }}

                    <div class="form-group">
                        @yield('submit')
                    </div>
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
                        {{ Form::bsCheckbox('roles[]', $role->id, $role->name, $user->hasRole($role), []) }}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection