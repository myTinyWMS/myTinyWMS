@extends('layout.app')

@section('content')
    @yield('form_start')
    <div class="w-full flex">
        <div class="w-1/3">
            <div class="card">
                <div class="card-header flex">
                    <h5 class="flex-1">@lang('Details')</h5>
                </div>
                <div class="card-content">
                    {{ Form::bsText('name', null, [], __('Name')) }}

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
                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                        {{ Form::bsCheckbox('permissions[]', $permission->id, $permission->name, $role->hasPermissionTo($permission), []) }}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection