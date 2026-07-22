<div><label class="block text-sm font-medium mb-1">Project Name</label><input name="name" value="{{ old('name', $project->name ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Description</label><textarea name="description" class="input-field">{{ old('description', $project->description ?? '') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Budget</label><input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget ?? 0) }}" class="input-field"></div>
<div><label class="block text-sm font-medium mb-1">Progress (%)</label><input type="number" min="0" max="100" name="progress" value="{{ old('progress', $project->progress ?? 0) }}" class="input-field"></div>
