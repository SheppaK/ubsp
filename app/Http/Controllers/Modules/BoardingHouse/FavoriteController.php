<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Favorite;
use App\Models\Modules\BoardingHouse\Property;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function index(): View
    {
        $favorites = Favorite::with(['property.rooms', 'property.reviews'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('modules.boarding-house.favorites.index', [
            'config' => $this->modules->get('boarding-house'),
            'favorites' => $favorites,
        ]);
    }

    public function store(Property $property): RedirectResponse
    {
        abort_unless($property->status === 'published', 404);

        Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'property_id' => $property->id,
        ]);

        return back()->with('success', 'Added to your wishlist.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        Favorite::where('user_id', auth()->id())
            ->where('property_id', $property->id)
            ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}
