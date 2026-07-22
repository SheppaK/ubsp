@extends('errors.layout')

@section('code', '401')
@section('title', 'Unauthorized')
@section('message', 'You need to sign in to access this page.')

@section('actions')
    <a href="{{ route('login') }}" class="btn-secondary text-sm">Log In</a>
@endsection
