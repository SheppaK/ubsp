<?php

namespace Database\Seeders;

use App\Models\Modules\BoardingHouse\Landlord;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\Room;
use App\Models\Modules\BoardingHouse\RoommatePost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BoardingHouseSeeder extends Seeder
{
    public function run(): void
    {
        $landlordUser = User::where('email', 'landlord@ubsp.local')->first()
            ?? User::where('email', 'admin@ubsp.local')->first();

        if (! $landlordUser) {
            return;
        }

        $landlord = Landlord::forUser($landlordUser);
        $landlord->update([
            'business_name' => 'Campus Stay Properties',
            'phone' => '+263771234567',
            'bio' => 'Verified landlord with 5+ years offering quality student accommodation near major universities.',
            'is_verified' => true,
        ]);

        $properties = [
            [
                'name' => 'Sunrise Student Lodge',
                'address' => '12 University Drive, Mount Pleasant',
                'city' => 'Harare',
                'latitude' => -17.7833,
                'longitude' => 31.0333,
                'description' => 'Modern boarding house with 24/7 security, high-speed WiFi, and walking distance to campus. Quiet study environment with shared kitchen and laundry facilities.',
                'distance_to_campus_km' => 1,
                'status' => 'published',
                'virtual_tour_video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'amenities' => ['WiFi', 'Security', 'Furnished', 'Kitchen', 'Laundry', 'Study Desk', 'Backup Power'],
                'rooms' => [
                    ['name' => 'Room A1', 'type' => 'single', 'price' => 85, 'capacity' => 1, 'description' => 'Spacious single room with desk and wardrobe.'],
                    ['name' => 'Room B2', 'type' => 'shared', 'price' => 55, 'capacity' => 2, 'description' => 'Shared room, 2 beds, ideal for friends.'],
                    ['name' => 'Studio Unit', 'type' => 'studio', 'price' => 120, 'capacity' => 1, 'description' => 'Self-contained studio with private bathroom.'],
                ],
            ],
            [
                'name' => 'Greenview Residences',
                'address' => '45 Avondale Road, Avondale',
                'city' => 'Harare',
                'latitude' => -17.7950,
                'longitude' => 31.0250,
                'description' => 'Affordable student housing with beautiful garden views. Electricity and water included in rent. Close to shops and transport.',
                'distance_to_campus_km' => 3,
                'status' => 'published',
                'virtual_tour_360_url' => 'https://momento360.com/e/u/placeholder-360-tour',
                'amenities' => ['WiFi', 'Electricity Included', 'Water Included', 'Parking', 'Kitchen', 'CCTV'],
                'rooms' => [
                    ['name' => 'Standard Single', 'type' => 'single', 'price' => 70, 'capacity' => 1, 'description' => 'Comfortable single room.'],
                    ['name' => 'Double Deluxe', 'type' => 'double', 'price' => 95, 'capacity' => 2, 'description' => 'Double room for couples or siblings.'],
                ],
            ],
            [
                'name' => 'Tech Hub Boarding',
                'address' => '8 Innovation Street, Belgravia',
                'city' => 'Harare',
                'latitude' => -17.8100,
                'longitude' => 31.0400,
                'description' => 'Premium boarding for tech students — fiber internet, air conditioning, and co-working space included.',
                'distance_to_campus_km' => 2,
                'status' => 'published',
                'amenities' => ['WiFi', 'Air Conditioning', 'Furnished', 'Study Desk', 'Security', 'Parking'],
                'rooms' => [
                    ['name' => 'Tech Single', 'type' => 'single', 'price' => 110, 'capacity' => 1, 'description' => 'AC single with ethernet port.'],
                    ['name' => 'Shared Pod', 'type' => 'shared', 'price' => 65, 'capacity' => 3, 'description' => '3-bed shared pod with lockers.'],
                ],
            ],
            [
                'name' => 'Budget Bachelors Pad',
                'address' => '22 Mbare Section 3',
                'city' => 'Harare',
                'latitude' => -17.8700,
                'longitude' => 31.0300,
                'description' => 'Budget-friendly option for students. Basic amenities, great community atmosphere.',
                'distance_to_campus_km' => 5,
                'status' => 'published',
                'amenities' => ['Water Included', 'Kitchen', 'Security'],
                'rooms' => [
                    ['name' => 'Economy Shared', 'type' => 'shared', 'price' => 35, 'capacity' => 4, 'description' => '4-bed dorm style room.'],
                ],
            ],
        ];

        foreach ($properties as $data) {
            $rooms = $data['rooms'];
            unset($data['rooms']);

            $property = $landlord->properties()->updateOrCreate(
                ['name' => $data['name']],
                $data
            );

            foreach ($rooms as $roomData) {
                $property->rooms()->updateOrCreate(
                    ['name' => $roomData['name'], 'property_id' => $property->id],
                    array_merge($roomData, ['is_available' => true])
                );
            }
        }

        $student = User::where('email', 'demo@ubsp.local')->first();
        if ($student) {
            RoommatePost::updateOrCreate(
                ['user_id' => $student->id, 'title' => 'Looking for quiet shared room near campus'],
                [
                    'bio' => 'Final-year engineering student, early sleeper, non-smoker. Looking for 1 roommate in a shared room under $70/mo.',
                    'budget' => 70,
                    'preferred_type' => 'shared',
                    'preferred_city' => 'Harare',
                    'is_active' => true,
                ]
            );
        }
    }
}
