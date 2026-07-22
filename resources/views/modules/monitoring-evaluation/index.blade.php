@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'] . ' — Projects')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <a href="{{ route('modules.monitoring-evaluation.dashboard') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Dashboard</a>
        <a href="{{ route('modules.monitoring-evaluation.projects.create') }}" class="btn-primary">Add Projects</a>
    </div>
    <div class="bento-card overflow-x-auto stagger-item">
        @if($projects->count())
            <table class="w-full text-sm">
                <thead><tr class="border-b border-brand-lavender/30 text-left font-sans text-brand-indigo/60">
                    <th class="py-3 pr-4">#</th><th class="py-3 pr-4">Name</th><th class="py-3 pr-4">Created</th><th class="py-3"></th>
                </tr></thead>
                <tbody>
                @foreach($projects as $item)
                    <tr class="border-b border-brand-lavender/20 hover:bg-brand-lavender/10 transition">
                        <td class="py-3 pr-4 font-sans">{{ $item->id }}</td>
                        <td class="py-3 pr-4 font-heading font-medium text-brand-indigo dark:text-brand-cream">{{ $item->name ?? $item->title ?? trim(($item->first_name ?? '').' '.($item->last_name ?? '')) ?: ($item->currency_code ?? $item->city ?? 'Record') }}</td>
                        <td class="py-3 pr-4 font-sans text-brand-indigo/50">{{ $item->created_at->diffForHumans() }}</td>
                        <td class="py-3 text-right"><a href="{{ route('modules.monitoring-evaluation.projects.edit', $item) }}" class="font-sans text-brand-coral hover:underline">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $projects->links() }}</div>
        @else
            <p class="text-center font-sans text-brand-indigo/50 py-12">No records yet.</p>
        @endif
    </div>
</div>
@endsection