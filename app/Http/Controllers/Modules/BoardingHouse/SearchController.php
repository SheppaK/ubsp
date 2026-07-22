<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Favorite;
use App\Models\Modules\BoardingHouse\Property;
use App\Services\ModuleManager;
use App\Services\Modules\BoardingHouse\CampusProximityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected CampusProximityService $proximity,
    ) {}

    public function index(Request $request): View
    {
        $query = Property::query()
            ->published()
            ->with(['rooms', 'reviews', 'landlord.user', 'images']);

        $query->search($request->input('q'));

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('max_price')) {
            $query->whereHas('rooms', fn ($q) => $q
                ->where('is_available', true)
                ->where('price', '<=', $request->max_price));
        }

        if ($request->filled('max_distance')) {
            $query->where('distance_to_campus_km', '<=', $request->max_distance);
        }

        if ($request->boolean('available_only')) {
            $query->whereHas('rooms', fn ($q) => $q->where('is_available', true));
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_low' => $query->withMin(['rooms as min_room_price' => fn ($q) => $q->where('is_available', true)], 'price')
                ->orderBy('min_room_price'),
            'price_high' => $query->withMin(['rooms as min_room_price' => fn ($q) => $q->where('is_available', true)], 'price')
                ->orderByDesc('min_room_price'),
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'distance' => $query->orderBy('distance_to_campus_km'),
            default => $query->latest(),
        };

        $properties = $query->paginate(12)->withQueryString();
        $cities = Property::published()->whereNotNull('city')->distinct()->pluck('city');

        $mapProperties = Property::published()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['rooms'])
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'lat' => (float) $p->latitude,
                'lng' => (float) $p->longitude,
                'price' => $p->minPrice(),
                'url' => route('modules.boarding-house.search.show', $p),
                'distance' => $p->computedDistanceKm(),
            ]);

        $favoriteIds = auth()->check()
            ? Favorite::where('user_id', auth()->id())->pluck('property_id')->all()
            : [];

        $compareIds = session(CompareController::SESSION_KEY, []);

        return view('modules.boarding-house.search.index', [
            'config' => $this->modules->get('boarding-house'),
            'properties' => $properties,
            'cities' => $cities,
            'filters' => $request->only(['q', 'city', 'max_price', 'max_distance', 'available_only', 'sort']),
            'mapProperties' => $mapProperties,
            'campus' => $this->proximity->campus(),
            'favoriteIds' => $favoriteIds,
            'compareIds' => $compareIds,
            'highlightId' => $request->integer('highlight') ?: null,
        ]);
    }

    public function show(Property $property): View
    {
        abort_unless($property->status === 'published' || $this->canManage($property), 404);

        $property->load(['rooms.availabilityBlocks', 'reviews.user', 'images', 'landlord.user']);

        return view('modules.boarding-house.search.show', [
            'config' => $this->modules->get('boarding-house'),
            'property' => $property,
            'canBook' => auth()->check() && $property->availableRoomsCount() > 0,
            'userReview' => auth()->check()
                ? $property->reviews()->where('user_id', auth()->id())->first()
                : null,
            'isFavorite' => auth()->check() && $property->isFavoritedBy(auth()->id()),
            'inCompare' => in_array($property->id, session(CompareController::SESSION_KEY, [])),
            'campusDistance' => $property->computedDistanceKm(),
            'campus' => $this->proximity->campus(),
        ]);
    }

    private function canManage(Property $property): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return true;
        }

        return $property->landlord?->user_id === $user->id;
    }
}
