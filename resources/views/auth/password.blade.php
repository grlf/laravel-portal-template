@extends('layouts.1col')


@section('content')

    @include('partials.global._validation_errors')

    <form method="POST" action="/password/email">
        {!! csrf_field() !!}

        <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}">
        </div>

        <div class="form-group">
            <button class="btn btn-default" type="submit">Send Password Reset Link</button>
        </div>
    </form>

@endsection
