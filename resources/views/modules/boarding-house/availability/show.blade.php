@extends('layouts.platform')

@section('title', 'Room Availability')
@section('header', $room->name.' — Calendar')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="hero-animate">
        <a href="{{ route('modules.boarding-house.search.show', $property) }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Back to property</a>
    </div>

    <div class="bento-card p-6 hero-animate">
        <h2 class="font-heading font-semibold text-brand-indigo">{{ $property->name }} · {{ $room->name }}</h2>
        <p class="font-sans text-sm text-brand-indigo/60 mt-1">${{ number_format($room->price) }}/month · {{ $room->typeLabel() }}</p>
    </div>

    <div class="bento-card p-6 stagger-item">
        <h3 class="font-heading font-semibold text-brand-indigo mb-4">Availability Calendar</h3>
        <div class="grid grid-cols-7 gap-1 text-center text-xs font-sans mb-2">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                <div class="font-medium text-brand-indigo/60 py-2">{{ $d }}</div>
            @endforeach
        </div>
        <div id="availability-calendar" class="grid grid-cols-7 gap-1" data-blocks='@json($blocks->map(fn($b) => ["start" => $b->start_date->format("Y-m-d"), "end" => $b->end_date->format("Y-m-d"), "type" => $b->type]))'></div>
        <div class="flex gap-4 mt-4 text-xs font-sans">
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-brand-lavender/50"></span> Available</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-brand-coral/60"></span> Booked</span>
            <span class="inline-flex items-center gap-1"><span class="w-3 h-3 rounded bg-brand-amber/60"></span> Blocked</span>
        </div>
    </div>

    @if(auth()->user()->hasAnyRole(['super-admin','administrator','landlord','manager']) || auth()->id() === $property->landlord?->user_id)
            <div class="bento-card p-6 stagger-item">
                <h3 class="font-heading font-semibold text-brand-indigo mb-4">Block Dates (Landlord)</h3>
                <form method="POST" action="{{ route('modules.boarding-house.admin.availability.store', [$property, $room]) }}" class="grid sm:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-sans text-brand-indigo/70">Start</label>
                        <input type="date" name="start_date" class="input-field" required>
                    </div>
                    <div>
                        <label class="text-sm font-sans text-brand-indigo/70">End</label>
                        <input type="date" name="end_date" class="input-field" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-secondary w-full">Block Dates</button>
                    </div>
                </form>
                @foreach($blocks->where('type', 'blocked') as $block)
                    <div class="flex justify-between items-center py-2 mt-2 border-t border-brand-lavender/20 text-sm font-sans">
                        <span>{{ $block->start_date->format('M d') }} – {{ $block->end_date->format('M d, Y') }}</span>
                        <form method="POST" action="{{ route('modules.boarding-house.admin.availability.destroy', [$property, $room, $block]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-brand-coral text-xs hover:underline">Remove</button>
                        </form>
                    </div>
                @endforeach
            </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('availability-calendar');
    if (!el) return;
    const blocks = JSON.parse(el.dataset.blocks || '[]');
    const now = new Date();
    const year = now.getFullYear(), month = now.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const inBlock = (dateStr) => {
        for (const b of blocks) {
            if (dateStr >= b.start && dateStr <= b.end) return b.type;
        }
        return null;
    };

    for (let i = 0; i < firstDay; i++) el.innerHTML += '<div></div>';
    for (let d = 1; d <= daysInMonth; d++) {
        const ds = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const type = inBlock(ds);
        const cls = type === 'booked' ? 'bg-brand-coral/60 text-white' : type === 'blocked' ? 'bg-brand-amber/60 text-brand-indigo' : 'bg-brand-lavender/30 text-brand-indigo';
        el.innerHTML += `<div class="aspect-square flex items-center justify-center rounded-lg text-xs font-sans ${cls}">${d}</div>`;
    }
});
</script>
@endpush
@endsection
