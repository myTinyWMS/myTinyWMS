@extends('layout.app')

@section('title', 'Passwort ändern')

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-content">
                    <form method="post" action="{{ route('settings.change_pw_post') }}">
                        <div class="row">

                            <div class="m-b-lg">
                                <label for="old_pw">altes Passwort</label>
                                <input type="password" class="form-control" name="old_pw" id="old_pw" value="">
                            </div>

                            <div class="m-b-lg">
                                <label for="new_pw">neues Passwort</label>
                                <input type="password" class="form-control" name="new_pw" id=new_pw" value="">
                            </div>

                            <div class="m-b-lg">
                                <label for="new_pw2">neues Passwort wiederholen</label>
                                <input type="password" class="form-control" name="new_pw2" id=new_pw2" value="">
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