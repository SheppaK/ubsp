<div><label class="block text-sm font-medium mb-1">Plan Name</label><input name="name" value="{{ old('name', $plan->name ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Provider</label><input name="provider" value="{{ old('provider', $plan->provider ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Monthly Cost</label><input type="number" step="0.01" name="monthly_cost" value="{{ old('monthly_cost', $plan->monthly_cost ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Max Members</label><input type="number" name="max_members" value="{{ old('max_members', $plan->max_members ?? 5) }}" class="input-field" required></div>
