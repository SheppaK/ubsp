<?php

return [
    'campus' => [
        'name' => env('BH_CAMPUS_NAME', 'Main Campus'),
        'latitude' => (float) env('BH_CAMPUS_LAT', -17.7833),
        'longitude' => (float) env('BH_CAMPUS_LNG', 31.0333),
    ],

    'holding_fee' => [
        'amount' => (float) env('BH_HOLDING_FEE_AMOUNT', 50),
        'currency' => env('BH_HOLDING_FEE_CURRENCY', 'usd'),
    ],

    'compare_max' => 3,

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
];
