@extends('layout.loginregister')

@section('content')
    <h3>Welcome to MSS</h3>

    <form class="m-t" role="form" method="post" action="{{ route('login') }}">
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
    <p class="m-t"> <small>Mail &copy; {{ date("Y") }}</small> </p>
@endsection
