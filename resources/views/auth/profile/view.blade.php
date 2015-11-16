@extends('layouts.1col');

@section('content')

    <div class="row">
        <div class="row">
            <div class="col-md-5">
                First Name:
            </div>
            <div class="col-md-7">
                {{ $user->first_name }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                Last Name:
            </div>
            <div class="col-md-7">
                {{ $user->last_name }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                User Name:
            </div>
            <div class="col-md-7">
                {{ $user->username }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-5">
                Email:
            </div>
            <div class="col-md-7">
                {{ $user->email }}
            </div>
        </div>
    </div>

@endsection