<div><label class="block text-sm font-medium mb-1">Objective ID</label><input type="number" name="objective_id" value="{{ old('objective_id', $kpi->objective_id ?? 1) }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">KPI Name</label><input name="name" value="{{ old('name', $kpi->name ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Target</label><input type="number" step="0.01" name="target" value="{{ old('target', $kpi->target ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Actual</label><input type="number" step="0.01" name="actual" value="{{ old('actual', $kpi->actual ?? 0) }}" class="input-field"></div>
