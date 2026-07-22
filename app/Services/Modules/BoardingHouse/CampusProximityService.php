<?php

namespace App\Services\Modules\BoardingHouse;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CampusProximityService
{
    public function campus(): array
    {
        return config('boarding-house.campus');
    }

    public function distanceKm(?float $lat, ?float $lng): ?float
    {
        if ($lat === null || $lng === null) {
            return null;
        }

        $campus = $this->campus();

        if ($apiKey = config('boarding-house.google_maps_api_key')) {
            return $this->googleDistance($lat, $lng, $campus['latitude'], $campus['longitude'], $apiKey)
                ?? $this->haversine($lat, $lng, $campus['latitude'], $campus['longitude']);
        }

        return $this->haversine($lat, $lng, $campus['latitude'], $campus['longitude']);
    }

    public function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return round($earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }

    private function googleDistance(float $lat, float $lng, float $campusLat, float $campusLng, string $apiKey): ?float
    {
        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => "{$lat},{$lng}",
                'destinations' => "{$campusLat},{$campusLng}",
                'key' => $apiKey,
            ]);

            $meters = $response->json('rows.0.elements.0.distance.value');

            return $meters ? round($meters / 1000, 1) : null;
        } catch (\Throwable $e) {
            Log::warning('Google Distance Matrix failed, falling back to Haversine', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
