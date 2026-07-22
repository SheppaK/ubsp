<?php

$base = dirname(__DIR__).'/resources/views/modules';
$formsDir = "{$base}/forms";
is_dir($formsDir) || mkdir($formsDir, 0755, true);

$modules = [
    'electronics-tracker' => ['slug' => 'electronics-tracker', 'route' => 'modules.electronics-tracker.assets', 'label' => 'Assets', 'var' => 'assets', 'record' => 'asset'],
    'university-social' => ['slug' => 'university-social', 'route' => 'modules.university-social.posts', 'label' => 'Posts', 'var' => 'posts', 'record' => 'post'],
    'balanced-scorecard' => ['slug' => 'balanced-scorecard', 'route' => 'modules.balanced-scorecard.kpis', 'label' => 'KPIs', 'var' => 'kpis', 'record' => 'kpi'],
    'marketplace' => ['slug' => 'marketplace', 'route' => 'modules.marketplace.products', 'label' => 'Products', 'var' => 'products', 'record' => 'product'],
    'boarding-house' => ['slug' => 'boarding-house', 'route' => 'modules.boarding-house.properties', 'label' => 'Properties', 'var' => 'properties', 'record' => 'property'],
    'exchange-tracker' => ['slug' => 'exchange-tracker', 'route' => 'modules.exchange-tracker.rates', 'label' => 'Rates', 'var' => 'rates', 'record' => 'rate'],
    'weather' => ['slug' => 'weather', 'route' => 'modules.weather.locations', 'label' => 'Locations', 'var' => 'locations', 'record' => 'location'],
    'clinic' => ['slug' => 'clinic', 'route' => 'modules.clinic.patients', 'label' => 'Patients', 'var' => 'patients', 'record' => 'patient'],
    'monitoring-evaluation' => ['slug' => 'monitoring-evaluation', 'route' => 'modules.monitoring-evaluation.projects', 'label' => 'Projects', 'var' => 'projects', 'record' => 'project'],
    'subscription-sharing' => ['slug' => 'subscription-sharing', 'route' => 'modules.subscription-sharing.plans', 'label' => 'Plans', 'var' => 'plans', 'record' => 'plan'],
    'sports-league' => ['slug' => 'sports-league', 'route' => 'modules.sports-league.leagues', 'label' => 'Leagues', 'var' => 'leagues', 'record' => 'league'],
];

$features = [
    'electronics-tracker' => ['Purchase date', 'Warranty', 'Serial numbers', 'QR Code', 'Maintenance', 'Disposal'],
    'university-social' => ['Posts', 'Likes', 'Comments', 'Events', 'Groups', 'Messaging'],
    'balanced-scorecard' => ['Objectives', 'KPIs', 'Targets', 'Reports', 'Traffic lights'],
    'marketplace' => ['Categories', 'Products', 'Wishlist', 'Reviews'],
    'boarding-house' => ['Properties', 'Rooms', 'Booking', 'Maps', 'Reviews'],
    'exchange-tracker' => ['Exchange rates', 'Fuel', 'Food', 'Alerts'],
    'weather' => ['Forecast', 'Temperature', 'Humidity', 'Wind'],
    'clinic' => ['Patients', 'Doctors', 'Appointments', 'Billing'],
    'monitoring-evaluation' => ['Projects', 'Indicators', 'Evidence', 'Dashboards'],
    'subscription-sharing' => ['Plans', 'Members', 'Renewals', 'Usage logs'],
    'sports-league' => ['Leagues', 'Teams', 'Fixtures', 'Standings'],
];

foreach ($modules as $key => $m) {
    $dir = "{$base}/{$key}";
    is_dir($dir) || mkdir($dir, 0755, true);
    $feat = var_export($features[$key], true);
    $dashRoute = "modules.{$m['slug']}.dashboard";

    file_put_contents("{$dir}/dashboard.blade.php", <<<BLADE
@extends('layouts.platform')
@section('title', \$config['name'])
@section('header', \$config['name'])
@section('content')
@php
    \$resourceRoute = '{$m['route']}.index';
    \$resourceLabel = '{$m['label']}';
    \$features = {$feat};
@endphp
@include('modules.partials.dashboard-content')
@endsection
BLADE);

    file_put_contents("{$dir}/index.blade.php", <<<BLADE
@extends('layouts.platform')
@section('title', \$config['name'])
@section('header', \$config['name'] . ' — {$m['label']}')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <a href="{{ route('{$dashRoute}') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Dashboard</a>
        <a href="{{ route('{$m['route']}.create') }}" class="btn-primary">Add {$m['label']}</a>
    </div>
    <div class="bento-card overflow-x-auto stagger-item">
        @if(\${$m['var']}->count())
            <table class="w-full text-sm">
                <thead><tr class="border-b border-brand-lavender/30 text-left font-sans text-brand-indigo/60">
                    <th class="py-3 pr-4">#</th><th class="py-3 pr-4">Name</th><th class="py-3 pr-4">Created</th><th class="py-3"></th>
                </tr></thead>
                <tbody>
                @foreach(\${$m['var']} as \$item)
                    <tr class="border-b border-brand-lavender/20 hover:bg-brand-lavender/10 transition">
                        <td class="py-3 pr-4 font-sans">{{ \$item->id }}</td>
                        <td class="py-3 pr-4 font-heading font-medium text-brand-indigo dark:text-brand-cream">{{ \$item->name ?? \$item->title ?? trim((\$item->first_name ?? '').' '.(\$item->last_name ?? '')) ?: (\$item->currency_code ?? \$item->city ?? 'Record') }}</td>
                        <td class="py-3 pr-4 font-sans text-brand-indigo/50">{{ \$item->created_at->diffForHumans() }}</td>
                        <td class="py-3 text-right"><a href="{{ route('{$m['route']}.edit', \$item) }}" class="font-sans text-brand-coral hover:underline">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ \${$m['var']}->links() }}</div>
        @else
            <p class="text-center font-sans text-brand-indigo/50 py-12">No records yet.</p>
        @endif
    </div>
</div>
@endsection
BLADE);

    $rec = $m['record'];
    file_put_contents("{$dir}/create.blade.php", <<<BLADE
@extends('layouts.platform')
@section('title', 'Create')
@section('header', 'Create {$m['label']}')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('{$m['route']}.index') }}" class="text-sm font-sans text-brand-indigo/70 hover:text-brand-coral transition">&larr; Back</a>
    <form method="POST" action="{{ route('{$m['route']}.store') }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf
        @include('modules.forms.{$key}')
        <button type="submit" class="btn-primary">Save</button>
    </form>
</div>
@endsection
BLADE);

    file_put_contents("{$dir}/edit.blade.php", <<<BLADE
@extends('layouts.platform')
@section('title', 'Edit')
@section('header', 'Edit {$m['label']}')
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('{$m['route']}.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back</a>
    <form method="POST" action="{{ route('{$m['route']}.update', \${$rec}) }}" class="bento-card mt-4 space-y-4 hero-animate">
        @csrf @method('PUT')
        @include('modules.forms.{$key}')
        <button type="submit" class="btn-primary">Update</button>
    </form>
</div>
@endsection
BLADE);
}

$formFields = [
    'electronics-tracker' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Name</label><input name="name" value="{{ old('name', $asset->name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Type</label><select name="type" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"><option value="computer">Computer</option><option value="phone">Phone</option><option value="laptop">Laptop</option><option value="printer">Printer</option><option value="accessory">Accessory</option></select></div>
<div><label class="block text-sm font-medium mb-1">Serial Number</label><input name="serial_number" value="{{ old('serial_number', $asset->serial_number ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Purchase Date</label><input type="date" name="purchase_date" value="{{ old('purchase_date', isset($asset) ? $asset->purchase_date?->format('Y-m-d') : '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
<div><label class="block text-sm font-medium mb-1">Warranty Expires</label><input type="date" name="warranty_expires" value="{{ old('warranty_expires', isset($asset) ? $asset->warranty_expires?->format('Y-m-d') : '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
<div><label class="block text-sm font-medium mb-1">Status</label><select name="status" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"><option value="active">Active</option><option value="maintenance">Maintenance</option><option value="disposed">Disposed</option></select></div>
<div><label class="block text-sm font-medium mb-1">Notes</label><textarea name="notes" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800">{{ old('notes', $asset->notes ?? '') }}</textarea></div>
HTML,
    'university-social' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Content</label><textarea name="content" rows="4" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required>{{ old('content', $post->content ?? '') }}</textarea></div>
HTML,
    'balanced-scorecard' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Objective ID</label><input type="number" name="objective_id" value="{{ old('objective_id', $kpi->objective_id ?? 1) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">KPI Name</label><input name="name" value="{{ old('name', $kpi->name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Target</label><input type="number" step="0.01" name="target" value="{{ old('target', $kpi->target ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Actual</label><input type="number" step="0.01" name="actual" value="{{ old('actual', $kpi->actual ?? 0) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
HTML,
    'marketplace' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Title</label><input name="title" value="{{ old('title', $product->title ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Description</label><textarea name="description" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required>{{ old('description', $product->description ?? '') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Price</label><input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Category ID</label><input type="number" name="category_id" value="{{ old('category_id', $product->category_id ?? 1) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
HTML,
    'boarding-house' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Landlord ID</label><input type="number" name="landlord_id" value="{{ old('landlord_id', $property->landlord_id ?? 1) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Name</label><input name="name" value="{{ old('name', $property->name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Address</label><textarea name="address" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required>{{ old('address', $property->address ?? '') }}</textarea></div>
HTML,
    'exchange-tracker' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Currency Code</label><input name="currency_code" maxlength="3" value="{{ old('currency_code', $rate->currency_code ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Rate</label><input type="number" step="0.000001" name="rate" value="{{ old('rate', $rate->rate ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Date</label><input type="date" name="recorded_date" value="{{ old('recorded_date', isset($rate) ? $rate->recorded_date?->format('Y-m-d') : date('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
HTML,
    'weather' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">City</label><input name="city" value="{{ old('city', $location->city ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Country</label><input name="country" value="{{ old('country', $location->country ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
<div><label class="block text-sm font-medium mb-1">Latitude</label><input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $location->latitude ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Longitude</label><input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $location->longitude ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
HTML,
    'clinic' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">First Name</label><input name="first_name" value="{{ old('first_name', $patient->first_name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Last Name</label><input name="last_name" value="{{ old('last_name', $patient->last_name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Phone</label><input name="phone" value="{{ old('phone', $patient->phone ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
<div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email" value="{{ old('email', $patient->email ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
HTML,
    'monitoring-evaluation' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Project Name</label><input name="name" value="{{ old('name', $project->name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Description</label><textarea name="description" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800">{{ old('description', $project->description ?? '') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Budget</label><input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget ?? 0) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
<div><label class="block text-sm font-medium mb-1">Progress (%)</label><input type="number" min="0" max="100" name="progress" value="{{ old('progress', $project->progress ?? 0) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
HTML,
    'subscription-sharing' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">Plan Name</label><input name="name" value="{{ old('name', $plan->name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Provider</label><input name="provider" value="{{ old('provider', $plan->provider ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Monthly Cost</label><input type="number" step="0.01" name="monthly_cost" value="{{ old('monthly_cost', $plan->monthly_cost ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Max Members</label><input type="number" name="max_members" value="{{ old('max_members', $plan->max_members ?? 5) }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
HTML,
    'sports-league' => <<<'HTML'
<div><label class="block text-sm font-medium mb-1">League Name</label><input name="name" value="{{ old('name', $league->name ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800" required></div>
<div><label class="block text-sm font-medium mb-1">Season</label><input name="season" value="{{ old('season', $league->season ?? '') }}" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"></div>
HTML,
];

foreach ($formFields as $key => $html) {
    file_put_contents("{$formsDir}/{$key}.blade.php", $html);
}

echo "Generated ".count($modules)." module view sets and forms.\n";
