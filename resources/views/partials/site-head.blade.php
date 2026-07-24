@php($brand = app(\App\Services\SiteBrandingService::class))

@if($brand->hasFavicon())
    <link rel="icon" href="{{ $brand->faviconUrl() }}" type="image/png">
    <link rel="shortcut icon" href="{{ $brand->faviconUrl() }}">
@endif
