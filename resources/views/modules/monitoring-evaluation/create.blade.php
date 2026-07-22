@extends('layouts.platform')
@section('title', 'Create')
@section('header', 'Create Projects')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.monitoring-evaluation.projects.index') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Back</a>
    <form method="POST" action="{{ route('modules.monitoring-evaluation.projects.store') }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf
        @include('modules.forms.monitoring-evaluation')
        <button type="submit" class="btn-primary">Save</button>
    </form>
</div>
@endsection