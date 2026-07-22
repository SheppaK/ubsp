@extends('layouts.platform')

@section('title', 'Landlord Analytics')
@section('header', 'Landlord Analytics')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
        <a href="{{ route('modules.boarding-house.admin.bookings.manage') }}" class="btn-secondary text-sm">Booking Inbox</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60">Properties</p>
            <p class="text-3xl font-heading font-bold text-brand-indigo">{{ $stats['properties'] }}</p>
        </div>
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60">Occupancy Rate</p>
            <p class="text-3xl font-heading font-bold text-brand-coral">{{ $stats['occupancy_rate'] }}%</p>
            <p class="text-xs font-sans text-brand-indigo/50">{{ $stats['occupied_rooms'] }}/{{ $stats['total_rooms'] }} rooms</p>
        </div>
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60">Total Revenue</p>
            <p class="text-3xl font-heading font-bold text-brand-indigo">${{ number_format($stats['revenue'], 0) }}</p>
        </div>
        <div class="stat-card stagger-item bento-card-accent">
            <p class="text-sm font-sans opacity-80">Pending Requests</p>
            <p class="text-3xl font-heading font-bold">{{ $stats['pending_bookings'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bento-card p-6 stagger-item">
            <h3 class="font-heading font-semibold text-brand-indigo mb-4">Monthly Revenue</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
        <div class="bento-card p-6 stagger-item">
            <h3 class="font-heading font-semibold text-brand-indigo mb-4">Review Distribution</h3>
            <canvas id="reviewChart" height="200"></canvas>
        </div>
    </div>

    <div class="bento-card p-6 stagger-item">
        <h3 class="font-heading font-semibold text-brand-indigo mb-4">Recent Bookings</h3>
        <div class="space-y-3">
            @forelse($recentBookings as $booking)
                <div class="flex justify-between items-center py-2 border-b border-brand-lavender/20 last:border-0">
                    <div>
                        <p class="font-sans text-sm text-brand-indigo">{{ $booking->room->property->name }} — {{ $booking->user->name }}</p>
                        <p class="text-xs text-brand-indigo/50">{{ $booking->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="tag text-xs capitalize">{{ $booking->status }}</span>
                </div>
            @empty
                <p class="font-sans text-brand-indigo/50">No bookings yet.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const revenueLabels = @json($monthlyRevenue->pluck('month'));
    const revenueData = @json($monthlyRevenue->pluck('total'));
    const reviewLabels = @json($reviewTrends->keys()->values());
    const reviewData = @json($reviewTrends->values());

    if (document.getElementById('revenueChart')) {
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: { labels: revenueLabels, datasets: [{ label: 'Revenue ($)', data: revenueData, backgroundColor: '#e07a5f' }] },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }
    if (document.getElementById('reviewChart')) {
        new Chart(document.getElementById('reviewChart'), {
            type: 'doughnut',
            data: { labels: reviewLabels, datasets: [{ data: reviewData, backgroundColor: ['#4a3f6b','#6b5b95','#9b8ec4','#e07a5f','#f2cc8f'] }] },
            options: { responsive: true }
        });
    }
});
</script>
@endpush
@endsection
