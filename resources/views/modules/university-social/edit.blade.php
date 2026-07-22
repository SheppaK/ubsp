@extends('layouts.platform')
@section('title', 'Edit')
@section('header', 'Edit Posts')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('modules.university-social.posts.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back</a>
    <form method="POST" action="{{ route('modules.university-social.posts.update', $post) }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf @method('PUT')
        @include('modules.forms.university-social')
        <button type="submit" class="btn-primary">Update</button>
    </form>
</div>
@endsection