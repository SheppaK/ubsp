<div><label class="block text-sm font-medium mb-1">Title</label><input name="title" value="{{ old('title', $product->title ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Description</label><textarea name="description" class="input-field" required>{{ old('description', $product->description ?? '') }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">Price</label><input type="number" step="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Category ID</label><input type="number" name="category_id" value="{{ old('category_id', $product->category_id ?? 1) }}" class="input-field" required></div>
