@extends('layouts.platform')

@section('title', 'Find Roommates')
@section('header', 'Roommate Matching')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bento-card p-6 hero-animate">
                <form method="GET" class="grid sm:grid-cols-3 gap-4">
                    <input type="text" name="city" value="{{ $filters['city'] ?? '' }}" placeholder="City" class="input-field">
                    <select name="type" class="input-field">
                        <option value="">Any room type</option>
                        @foreach(['single','double','shared','studio','any'] as $t)
                            <option value="{{ $t }}" @selected(($filters['type'] ?? '') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary">Filter</button>
                </form>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                @forelse($posts as $post)
                    <div class="bento-card stagger-item p-6" data-hover-lift>
                        <h3 class="font-heading font-semibold text-brand-indigo">{{ $post->title }}</h3>
                        <p class="font-sans text-sm text-brand-indigo/60 mt-1">{{ $post->user->name }}</p>
                        <p class="font-sans text-sm text-brand-indigo/80 mt-3">{{ Str::limit($post->bio, 120) }}</p>
                        <div class="flex flex-wrap gap-2 mt-4">
                            @if($post->budget)<span class="tag text-xs">Budget: ${{ $post->budget }}/mo</span>@endif
                            <span class="tag text-xs">{{ $post->typeLabel() }}</span>
                            @if($post->preferred_city)<span class="tag text-xs">{{ $post->preferred_city }}</span>@endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bento-card text-center py-12">
                        <p class="font-heading text-brand-indigo">No roommate posts yet</p>
                    </div>
                @endforelse
            </div>
            {{ $posts->links() }}
        </div>

        <div class="space-y-4">
            <div class="bento-card-dark p-6 sticky top-24">
                <h3 class="font-heading font-semibold text-brand-cream mb-4">Post Your Profile</h3>
                <form method="POST" action="{{ route('modules.boarding-house.roommates.store') }}" class="space-y-3">
                    @csrf
                    <input type="text" name="title" class="input-field text-sm" placeholder="Headline" required>
                    <textarea name="bio" rows="3" class="input-field text-sm" placeholder="About you, habits, schedule..." required></textarea>
                    <input type="number" name="budget" class="input-field text-sm" placeholder="Max budget/mo">
                    <select name="preferred_type" class="input-field text-sm" required>
                        @foreach(['any','single','double','shared','studio'] as $t)
                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="preferred_city" class="input-field text-sm" placeholder="Preferred city">
                    <button type="submit" class="btn-primary w-full text-sm">Post Profile</button>
                </form>
            </div>

            @if($myPosts->count())
                <div class="bento-card p-5">
                    <h4 class="font-heading font-semibold text-brand-indigo mb-3">My Posts</h4>
                    @foreach($myPosts as $post)
                        <div class="flex justify-between items-center py-2 border-b border-brand-lavender/20 last:border-0">
                            <span class="font-sans text-sm">{{ $post->title }}</span>
                            <form method="POST" action="{{ route('modules.boarding-house.roommates.destroy', $post) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-brand-coral hover:underline">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
