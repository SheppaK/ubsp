@extends('layouts.platform')
@section('title', 'Edit')
@section('header', 'Edit Locations')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.weather.locations.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back</a>
    <form method="POST" action="{{ route('modules.weather.locations.update', $location) }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf @method('PUT')
        @include('modules.forms.weather')
        <button type="submit" class="btn-primary">Update</button>
    </form>
</div>
@endsection