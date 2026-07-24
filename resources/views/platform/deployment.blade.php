@extends('layouts.platform')

@section('title', 'Deployment')
@section('header', 'Deployment & Maintenance')

@section('content')
<div class="max-w-3xl space-y-6">
    @if(session('deploy_result'))
        @php $result = session('deploy_result'); @endphp
        <div class="bento-card p-4 font-mono text-sm {{ ($result['success'] ?? false) ? 'border-green-300 bg-green-50 text-green-900' : 'border-brand-coral/30 bg-brand-coral/5 text-brand-coral' }}">
            <p class="font-heading font-semibold mb-2">{{ ($result['success'] ?? false) ? 'Success' : 'Failed' }}: {{ $result['command'] ?? 'command' }}</p>
            <pre class="whitespace-pre-wrap break-words">{{ $result['output'] ?? '' }}</pre>
        </div>
    @endif

    <p class="font-sans text-brand-indigo/70">Pull code updates and run Laravel maintenance commands. Actions are logged. Restricted to super-admin only.</p>

    <div class="bento-card p-5 font-sans text-sm space-y-2">
        <p class="font-heading font-semibold text-brand-indigo">Environment</p>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-brand-indigo/80">
            <div><dt class="inline text-brand-indigo/50">PHP:</dt> {{ $info['php_version'] }}</div>
            <div><dt class="inline text-brand-indigo/50">Laravel:</dt> {{ $info['laravel_version'] }}</div>
            <div><dt class="inline text-brand-indigo/50">Environment:</dt> {{ $info['app_env'] }}</div>
            <div><dt class="inline text-brand-indigo/50">Debug:</dt> {{ $info['debug'] ? 'on' : 'off' }}</div>
            <div><dt class="inline text-brand-indigo/50">Git repo:</dt> {{ $info['git_repo'] ? 'yes' : 'no' }}</div>
            <div><dt class="inline text-brand-indigo/50">Vite manifest:</dt> {{ $info['manifest_exists'] ? 'yes' : 'missing — upload public/build/' }}</div>
            <div><dt class="inline text-brand-indigo/50">Storage link:</dt> {{ $info['storage_linked'] ? 'yes' : 'no' }}</div>
            <div><dt class="inline text-brand-indigo/50">Shell (git):</dt>
                {{ ($info['exec_available'] || $info['shell_exec_available'] || $info['proc_open_available']) ? 'available' : 'disabled on server' }}
            </div>
        </dl>
    </div>

    @if($gitEnabled && $info['git_repo'])
        <div class="bento-card p-6 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-heading font-bold text-brand-indigo">Git</h2>
                    <p class="font-sans text-sm text-brand-indigo/60">Branch: <code>{{ $info['git_branch'] }}</code></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('platform.deployment.git-status') }}">
                        @csrf
                        <button type="submit" class="btn-secondary text-sm">Refresh status</button>
                    </form>
                    <form method="POST" action="{{ route('platform.deployment.git-pull') }}" onsubmit="return confirm('Pull latest code from git?');">
                        @csrf
                        <button type="submit" class="btn-primary text-sm">Git pull</button>
                    </form>
                </div>
            </div>
            @if($gitStatus)
                <pre class="rounded-xl bg-brand-indigo/5 p-4 font-mono text-xs text-brand-indigo/80 whitespace-pre-wrap overflow-x-auto">{{ $gitStatus['output'] }}</pre>
            @endif
        </div>
    @elseif($gitEnabled)
        <div class="bento-card p-4 font-sans text-sm text-brand-indigo/70">
            Git pull is enabled but this folder is not a git clone. Clone the repo on the server or upload via FTP.
        </div>
    @endif

    <div class="bento-card p-6 space-y-4">
        <h2 class="font-heading font-bold text-brand-indigo">Laravel commands</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($commands as $key => $label)
                <form method="POST" action="{{ route('platform.deployment.artisan') }}"
                      @if($key === 'migrate') onsubmit="return confirm('Run database migrations?');" @endif>
                    @csrf
                    <input type="hidden" name="command" value="{{ $key }}">
                    <button type="submit" class="w-full btn-secondary text-sm text-left">{{ $label }}</button>
                </form>
            @endforeach
        </div>
    </div>

    <div class="bento-card p-6">
        <h2 class="font-heading font-bold text-brand-indigo mb-2">Storage link</h2>
        <p class="font-sans text-sm text-brand-indigo/60 mb-4">Create <code>public/storage</code> symlink (Hostinger-safe, no PHP exec).</p>
        <form method="POST" action="{{ route('platform.deployment.storage-link') }}">
            @csrf
            <button type="submit" class="btn-secondary text-sm">Link storage</button>
        </form>
    </div>

    <div class="bento-card p-4 font-sans text-xs text-brand-indigo/50 space-y-1">
        <p>After <strong>git pull</strong>, run <strong>Run migrations</strong> if there are new migrations.</p>
        <p>After code changes, run <strong>Clear all caches</strong> or <strong>Optimize</strong>.</p>
        <p><code>public/build/</code> is not in git — rebuild with <code>npm run build</code> locally and upload after frontend changes.</p>
    </div>
</div>
@endsection
