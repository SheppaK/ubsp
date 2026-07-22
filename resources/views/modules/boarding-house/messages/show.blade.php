@extends('layouts.platform')

@section('title', 'Chat')
@section('header', $conversation->bookingRequest->room->property->name)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <a href="{{ route('modules.boarding-house.messages.index') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral hero-animate">&larr; All messages</a>

    <div class="bento-card p-4 hero-animate">
        <p class="font-sans text-sm text-brand-indigo/70">
            Booking #{{ $conversation->bookingRequest->id }} · {{ $conversation->bookingRequest->room->name }}
            · <span class="capitalize">{{ $conversation->bookingRequest->status }}</span>
        </p>
    </div>

    <div class="bento-card p-6 space-y-4 min-h-[320px] max-h-[480px] overflow-y-auto stagger-item" id="chat-thread">
        @forelse($conversation->messages as $message)
            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[75%] rounded-2xl px-4 py-3 {{ $message->user_id === auth()->id() ? 'bg-brand-indigo text-brand-cream' : 'bg-brand-lavender/30 text-brand-indigo' }}">
                    <p class="text-xs font-sans opacity-70 mb-1">{{ $message->user->name }}</p>
                    <p class="font-sans text-sm">{{ $message->body }}</p>
                    <p class="text-xs opacity-50 mt-1">{{ $message->created_at->format('M d, H:i') }}</p>
                </div>
            </div>
        @empty
            <p class="font-sans text-brand-indigo/50 text-center py-8">No messages yet. Start the conversation!</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('modules.boarding-house.messages.store', $conversation) }}" class="bento-card p-4 flex gap-3 stagger-item">
        @csrf
        <input type="text" name="body" class="input-field flex-1" placeholder="Type a message..." required autofocus>
        <button type="submit" class="btn-primary shrink-0">Send</button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const thread = document.getElementById('chat-thread');
    if (thread) thread.scrollTop = thread.scrollHeight;
});
</script>
@endpush
@endsection
