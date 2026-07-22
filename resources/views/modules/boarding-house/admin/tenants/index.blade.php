@extends('layouts.platform')

@section('title', 'Tenants')
@section('header', 'Manage Tenants')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bento-card p-4 border-brand-amber bg-brand-amber font-sans text-brand-indigo">{{ session('success') }}</div>
    @endif
    @if(session('temp_password'))
        <div class="bento-card p-4 border-2 border-brand-coral bg-brand-cream">
            <p class="font-sans text-sm text-body"><strong>Temporary password (email failed):</strong></p>
            <code class="font-mono text-lg text-brand-coral">{{ session('temp_password') }}</code>
            <p class="font-sans text-xs text-muted mt-2">Share this with the tenant securely. They can reset it via Forgot Password.</p>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="font-sans text-brand-indigo/70">Tenant accounts for {{ $business->name }}. Login credentials are emailed on creation; tenants can reset passwords anytime.</p>
        <a href="{{ route('modules.boarding-house.admin.tenants.create') }}" class="btn-primary">Add Tenant</a>
    </div>

    <div class="bento-card overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-brand-lavender/10">
                <tr>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Name</th>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Email</th>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Phone</th>
                    <th class="px-4 py-3 font-heading text-sm text-brand-indigo">Added</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-lavender/20">
                @forelse($tenants as $member)
                    <tr class="font-sans text-sm text-brand-indigo/80">
                        <td class="px-4 py-3">{{ $member->user->name }}</td>
                        <td class="px-4 py-3">{{ $member->user->email }}</td>
                        <td class="px-4 py-3">{{ $member->user->phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-brand-indigo/50">{{ $member->created_at->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-brand-indigo/50">No tenants yet. Create accounts for your boarding house tenants.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tenants->links() }}
</div>
@endsection
