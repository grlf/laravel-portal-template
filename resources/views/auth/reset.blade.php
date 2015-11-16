@extends('layouts.1col')

@section('content')

    @include('partials.global._validation_errors')

    <form method="POST" action="/password/reset">
        {!! csrf_field() !!}
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <input class="form-control" id="password" type="password" name="password">
        </div>

        <div class="form-group">
            <label for="password_confirmation">New Password Confirmation</label>
            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation">
        </div>

        <div class="form-group">
            <button class="btn btn-default" type="submit">Reset Password</button>
        </div>
    </form>

@endsection
