@extends('layouts.platform')

@section('title', 'Edit Property')
@section('header', 'Edit: '.$property->name)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('modules.boarding-house.admin.properties.show', $property) }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Back</a>

    <form method="POST" action="{{ route('modules.boarding-house.admin.properties.update', $property) }}" enctype="multipart/form-data" class="bento-card mt-4 p-8 space-y-6 hero-animate">
        @csrf @method('PUT')
        @include('modules.boarding-house.partials.property-form', ['property' => $property, 'amenityOptions' => $amenityOptions])
        <button type="submit" class="btn-primary">Save Changes</button>
    </form>
</div>
@endsection
