@extends('layouts.platform')
@section('title', 'Create')
@section('header', 'Create Locations')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.weather.locations.index') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Back</a>
    <form method="POST" action="{{ route('modules.weather.locations.store') }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf
        @include('modules.forms.weather')
        <button type="submit" class="btn-primary">Save</button>
    </form>
</div>
@endsection