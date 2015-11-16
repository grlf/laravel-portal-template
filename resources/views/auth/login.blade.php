@extends('layouts.1col')

@section('content')

    @include('partials.global._validation_errors')

    <form method="POST" action="/auth/login">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" type="text" name="username" value="{{ old('username') }}">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" name="password" id="password">
            <small><a href="/password/email">Click here to reset your password</a></small>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="remember"> Remember Me
            </label>
        </div>

        <div class="form-group">
            <button class="btn btn-default" type="submit">Login</button>
        </div>
    </form>

@endsection