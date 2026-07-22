@extends('layouts.platform')

@section('title', 'Profile')
@section('header', 'Profile Settings')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bento-card hero-animate p-8">
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="bento-card stagger-item p-8">
        @include('profile.partials.update-password-form')
    </div>
    <div class="bento-card stagger-item p-8">
        @include('profile.partials.delete-user-form')
    </div>
    <div class="bento-card stagger-item p-8">
        <h3 class="font-heading font-semibold text-brand-indigo dark:text-brand-cream mb-2">Two-Factor Authentication</h3>
        <p class="text-sm font-sans text-brand-indigo/60 dark:text-brand-lavender mb-4">Add an extra layer of security to your account.</p>
        <a href="{{ route('two-factor.show') }}" class="btn-secondary text-sm">Manage 2FA</a>
    </div>
</div>
@endsection
