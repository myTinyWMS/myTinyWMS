@extends('layout.app')

@section('title', __('Administrator'))

@section('content')

    <div class="flex -ml-6">
        <div class="bg-white border border-t-8 border-gray-700 w-1/5 m-6 p-6 text-center rounded shadow">
            <a href="{{ url('/admin/settings') }}"><i class="fa fa-cogs text-6xl text-blue-700 mb-8 block"></i></a>
            @lang('Einstellungen')
        </div>

        <div class="bg-white border border-t-8 border-gray-700 w-1/5 m-6 p-6 text-center rounded shadow">
            <a href="{{ url('/admin/unit') }}"><i class="fa fa-balance-scale text-6xl text-blue-700 mb-8 block"></i></a>
            @lang('Einheiten')
        </div>

        <div class="bg-white border border-t-8 border-gray-700 w-1/5 m-6 p-6 text-center rounded shadow">
            <a href="{{ url('/admin/category') }}"><i class="fa fa-cubes text-6xl text-blue-700 mb-8 block"></i></a>
            @lang('Kategorien')
        </div>

        <div class="bg-white border border-t-8 border-gray-700 w-1/5 m-6 p-6 text-center rounded shadow">
            <a href="{{ url('/admin/user') }}"><i class="fa fa-users text-6xl text-blue-700 mb-8 block"></i></a>
            @lang('Benutzerverwaltung')
        </div>
    </div>
@endsection
