@extends('layouts.1col')

@section('content')

    @include('partials.global._validation_errors')

    <form method="POST" action="/auth/register">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input class="form-control" id="first_name" type="text" name="first_name" value="{{ old('first_name') }}">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input class="form-control" id="last_name" type="text" name="last_name" value="{{ old('last_name') }}">
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" type="text" name="username" value="{{ old('username') }}">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" id="password" type="password" name="password">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Password</label>
            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation">
        </div>

        <div class="form-group">
            <button class="btn btn-default" type="submit">Register</button>
        </div>
    </form>

@endsection