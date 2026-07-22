@extends('errors.layout')

@section('code', '403')
@section('title', 'Access Denied')
@section('message', 'You do not have permission to view this resource.')

@section('actions')
    @auth
        <a href="{{ route('platform.dashboard') }}" class="btn-secondary text-sm">Dashboard</a>
    @else
        <a href="{{ route('login') }}" class="btn-secondary text-sm">Log In</a>
    @endauth
@endsection
