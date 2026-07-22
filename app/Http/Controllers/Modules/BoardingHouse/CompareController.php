<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Property;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    public const SESSION_KEY = 'bh_compare';

    public function __construct(protected ModuleManager $modules) {}

    public function index(): View
    {
        $ids = session(self::SESSION_KEY, []);
        $properties = Property::published()
            ->with(['rooms', 'reviews', 'images'])
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->values();

        return view('modules.boarding-house.compare.index', [
            'config' => $this->modules->get('boarding-house'),
            'properties' => $properties,
        ]);
    }

    public function store(Property $property): RedirectResponse
    {
        abort_unless($property->status === 'published', 404);

        $ids = session(self::SESSION_KEY, []);
        $max = config('boarding-house.compare_max', 3);

        if (! in_array($property->id, $ids)) {
            if (count($ids) >= $max) {
                return back()->withErrors(['compare' => "You can compare up to {$max} properties. Remove one first."]);
            }
            $ids[] = $property->id;
            session([self::SESSION_KEY => $ids]);
        }

        return back()->with('success', 'Added to compare list.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $ids = array_values(array_filter(
            session(self::SESSION_KEY, []),
            fn ($id) => $id != $property->id
        ));
        session([self::SESSION_KEY => $ids]);

        return back()->with('success', 'Removed from compare.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget(self::SESSION_KEY);

        return redirect()->route('modules.boarding-house.search.index');
    }
}
