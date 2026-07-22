@extends('layouts.platform')

@section('title', 'Add Property')
@section('header', 'List New Property')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('modules.boarding-house.admin.properties.index') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Back</a>

    <form method="POST" action="{{ route('modules.boarding-house.admin.properties.store') }}" enctype="multipart/form-data" class="bento-card mt-4 p-8 space-y-6 hero-animate">
        @csrf
        @include('modules.boarding-house.partials.property-form', ['property' => null, 'amenityOptions' => $amenityOptions])
        <button type="submit" class="btn-primary">Create Property</button>
    </form>
</div>
@endsection
