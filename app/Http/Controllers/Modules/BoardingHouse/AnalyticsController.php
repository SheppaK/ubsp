<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Landlord;
use App\Models\Modules\BoardingHouse\Payment;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\Review;
use App\Models\Modules\BoardingHouse\Room;
use App\Services\ModuleManager;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function __invoke(): View
    {
        $user = auth()->user();
        $landlord = Landlord::where('user_id', $user->id)->first();
        $isAdmin = $user->hasAnyRole(['super-admin', 'administrator']);

        $propertyQuery = Property::query();
        if (! $isAdmin && $landlord) {
            $propertyQuery->where('landlord_id', $landlord->id);
        } elseif (! $isAdmin) {
            abort(403);
        }

        $propertyIds = $propertyQuery->pluck('id');
        $roomIds = Room::whereIn('property_id', $propertyIds)->pluck('id');

        $totalRooms = $roomIds->count();
        $occupiedRooms = Room::whereIn('id', $roomIds)->where('is_available', false)->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $revenue = Payment::where('status', 'paid')
            ->whereHas('bookingRequest.room', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->sum('amount');

        $monthlyRevenue = Payment::where('status', 'paid')
            ->whereHas('bookingRequest.room', fn ($q) => $q->whereIn('property_id', $propertyIds))
            ->whereNotNull('paid_at')
            ->get()
            ->groupBy(fn ($p) => $p->paid_at->format('Y-m'))
            ->map(fn ($group, $month) => (object) ['month' => $month, 'total' => $group->sum('amount')])
            ->sortKeys()
            ->values();

        $reviewTrends = Review::whereIn('property_id', $propertyIds)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating');

        $bookingStats = BookingRequest::whereIn('room_id', $roomIds)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentBookings = BookingRequest::with(['room.property', 'user'])
            ->whereIn('room_id', $roomIds)
            ->latest()
            ->take(5)
            ->get();

        return view('modules.boarding-house.admin.analytics', [
            'config' => $this->modules->get('boarding-house'),
            'stats' => [
                'properties' => $propertyIds->count(),
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'occupancy_rate' => $occupancyRate,
                'revenue' => $revenue,
                'pending_bookings' => $bookingStats['pending'] ?? 0,
                'approved_bookings' => $bookingStats['approved'] ?? 0,
            ],
            'monthlyRevenue' => $monthlyRevenue,
            'reviewTrends' => $reviewTrends,
            'recentBookings' => $recentBookings,
        ]);
    }
}
