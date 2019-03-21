@extends('layout.loginregister')

@section('content')
    <div class="w-full max-w-xs">
        <h2 class="text-center mb-4">Welcome to MSS</h2>

        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="{{ route('login') }}" method="post">
            {{ csrf_field() }}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="login">
                    Benutzername / E-Mail
                </label>
                <input class="form-input {{ $errors->has('username') || $errors->has('email') ? ' has-error' : '' }}" id="login" name="login" value="{{ old('login') }}" type="text" placeholder="" required>
                <span class="block mt-2 mb-4 text-gray-600 text-xs">Beim ersten Login bitte mit E-Mail Adresse einloggen</span>

                @if ($errors->has('username'))
                    <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('username') }}</p>
                @endif
                @if ($errors->has('email'))
                    <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('email') }}</p>
                @endif
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="form-input {{ $errors->has('password') ? ' has-error' : '' }}" id="password" name="password" type="password" placeholder="" required>

                @if ($errors->has('password'))
                    <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('password') }}</p>
                @endif
            </div>
            <div class="flex items-center justify-between">
                <button class="btn btn-primary" type="submit">
                    Login
                </button>
                <a class="btn-link text-sm" href="{{ route('password.request') }}">
                    Passwort vergessen?
                </a>
            </div>
        </form>
    </div>

   {{-- <form class="m-t" role="form" method="post" action="{{ route('login') }}">
        {{ csrf_field() }}
        <div class="form-group{{ $errors->has('login') ? ' has-error' : '' }}">
            <input id="login" type="text" class="form-control" name="login" value="{{ old('login') }}" placeholder="E-Mail oder Username" required autofocus>

            @if ($errors->has('email'))
                <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
            @endif
            <span class="help-block">Beim ersten Login bitte mit E-Mail Adresse einloggen</span>
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>

            @if ($errors->has('password'))
                <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

        <a href="{{ route('password.request') }}"><small>Passwort vergessen?</small></a>
    </form>
    <p class="m-t"> <small>Mail &copy; {{ date("Y") }}</small> </p>--}}
@endsection
