<div><label class="block text-sm font-medium mb-1">City</label><input name="city" value="{{ old('city', $location->city ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Country</label><input name="country" value="{{ old('country', $location->country ?? '') }}" class="input-field"></div>
<div><label class="block text-sm font-medium mb-1">Latitude</label><input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $location->latitude ?? '') }}" class="input-field" required></div>
<div><label class="block text-sm font-medium mb-1">Longitude</label><input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $location->longitude ?? '') }}" class="input-field" required></div>
