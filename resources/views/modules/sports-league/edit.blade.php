@extends('layouts.platform')
@section('title', 'Edit')
@section('header', 'Edit Leagues')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.sports-league.leagues.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back</a>
    <form method="POST" action="{{ route('modules.sports-league.leagues.update', $league) }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf @method('PUT')
        @include('modules.forms.sports-league')
        <button type="submit" class="btn-primary">Update</button>
    </form>
</div>
@endsection