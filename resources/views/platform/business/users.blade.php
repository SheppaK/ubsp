@extends('layouts.platform')

@section('title', 'Team Members')
@section('header', 'Team Members — '.$business->name)

@section('content')
<div class="space-y-6">
    <a href="{{ route('platform.business.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Back to Business</a>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="font-sans text-brand-indigo/70">Users linked to your business account.</p>
        @if($business->hasModule('boarding-house'))
            <a href="{{ route('modules.boarding-house.admin.tenants.create') }}" class="btn-primary">Add Tenant Account</a>
        @endif
    </div>

    <div class="bento-card overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-brand-lavender/10">
                <tr>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Name</th>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Email</th>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Role</th>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Added</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-lavender/20">
                @forelse($members as $member)
                    <tr class="font-sans text-sm text-brand-indigo/80">
                        <td class="px-4 py-3">{{ $member->user->name }}</td>
                        <td class="px-4 py-3">{{ $member->user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-lg text-xs capitalize {{ $member->role === 'owner' ? 'bg-brand-amber/30' : 'bg-brand-lavender/20' }}">
                                {{ $member->role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-brand-indigo/50">{{ $member->created_at->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-brand-indigo/50">No team members yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $members->links() }}
</div>
@endsection
