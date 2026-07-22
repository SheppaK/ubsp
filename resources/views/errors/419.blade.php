@extends('errors.layout')

@section('code', '419')
@section('title', 'Session Expired')
@section('message', 'Your session or security token expired. This usually happens when a page was open too long. Please refresh and try again.')

@section('actions')
    <a href="{{ route('login') }}" class="btn-primary text-sm">Back to Login</a>
@endsection
