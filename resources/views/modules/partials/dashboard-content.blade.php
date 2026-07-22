<div class="space-y-6">
    <div class="bento-card hero-animate p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-heading text-2xl font-bold text-brand-indigo dark:text-brand-cream">{{ $config['name'] }}</h2>
                <p class="font-sans text-brand-indigo/60 dark:text-brand-lavender mt-1">{{ $config['description'] }}</p>
            </div>
            @if(!empty($resourceRoute))
                <a href="{{ route($resourceRoute) }}" class="btn-primary shrink-0">Manage {{ $resourceLabel }}</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60 dark:text-brand-lavender">Total {{ $resourceLabel }}</p>
            <p class="text-3xl font-heading font-bold text-brand-indigo dark:text-brand-cream" data-count="{{ $stats['count'] ?? 0 }}">{{ $stats['count'] ?? 0 }}</p>
        </div>
        <div class="stat-card stagger-item bento-card-accent">
            <p class="text-sm font-sans opacity-80">Module Status</p>
            <p class="text-lg font-heading font-bold">Active</p>
        </div>
    </div>

    @if(!empty($features))
        <div class="bento-card stagger-item p-8">
            <h3 class="font-heading font-semibold mb-4 text-brand-indigo dark:text-brand-cream">Features</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($features as $feature)
                    <span class="tag">{{ $feature }}</span>
                @endforeach
            </div>
        </div>
    @endif
</div>
