@extends('layouts.platform')
@section('title', 'Create')
@section('header', 'Create Rates')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.exchange-tracker.rates.index') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Back</a>
    <form method="POST" action="{{ route('modules.exchange-tracker.rates.store') }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf
        @include('modules.forms.exchange-tracker')
        <button type="submit" class="btn-primary">Save</button>
    </form>
</div>
@endsection