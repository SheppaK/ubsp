@extends('layouts.platform')
@section('title', 'Create')
@section('header', 'Create Assets')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.electronics-tracker.assets.index') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Back</a>
    <form method="POST" action="{{ route('modules.electronics-tracker.assets.store') }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf
        @include('modules.forms.electronics-tracker')
        <button type="submit" class="btn-primary">Save</button>
    </form>
</div>
@endsection