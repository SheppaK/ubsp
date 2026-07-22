<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        abort_unless($property->status === 'published', 404);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        Review::updateOrCreate(
            [
                'property_id' => $property->id,
                'user_id' => $request->user()->id,
            ],
            $validated
        );

        return back()->with('success', 'Thank you for your review!');
    }
}
