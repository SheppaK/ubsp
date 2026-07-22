<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Landlord;
use App\Models\Modules\BoardingHouse\Property;
use App\Services\ModuleManager;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function __invoke(): View
    {
        $user = auth()->user();
        $isManager = $user->hasAnyRole(['super-admin', 'administrator', 'landlord', 'manager', 'business-owner'])
            || $user->can('boarding-house.manage-properties');

        $stats = [
            'properties' => Property::when(! $isManager, fn ($q) => $q->published())->count(),
            'available_rooms' => Property::when(! $isManager, fn ($q) => $q->published())
                ->withCount(['rooms as available' => fn ($q) => $q->where('is_available', true)])
                ->get()->sum('available'),
            'my_bookings' => BookingRequest::where('user_id', $user->id)->count(),
            'pending_bookings' => $isManager
                ? BookingRequest::where('status', 'pending')
                    ->when(! $user->hasAnyRole(['super-admin', 'administrator']), function ($q) use ($user) {
                        $q->whereHas('room.property.landlord', fn ($lq) => $lq->where('user_id', $user->id));
                    })
                    ->count()
                : 0,
        ];

        return view('modules.boarding-house.dashboard', [
            'config' => $this->modules->get('boarding-house'),
            'stats' => $stats,
            'isManager' => $isManager,
            'recentProperties' => Property::published()
                ->with(['rooms', 'reviews'])
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}
