@extends('layout.handscanner')

@section('content')
    <h2 class="text-center m-b-lg">Login</h2>

    <form method="post" action="{{ route('handscanner.processlogin') }}">

        <div class="form-group">
            <label for="user">Benutzer auswählen</label>
            <select class="form-control input-lg" name="user" id="user">
                @foreach(\Mss\Models\UserSettings::getUsersWhereHas(\Mss\Models\UserSettings::SETTINGS_HANDSCANNER_PIN_CODE)->sortBy('name') as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="pin">PIN Code</label>
            <input type="password" class="form-control input-lg m-b-lg text-center" name="pin" id="pin">
        </div>
        <button class="btn btn-primary btn-lg btn-block">Einloggen</button>
        {{ csrf_field() }}
    </form>
@endsection