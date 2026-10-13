<?php

return [
    // Une seule adresse centrale pour toutes les alertes destinées à Kessy.
    'admin_email' => env('JORSID_ADMIN_EMAIL', ''),
    'business_name' => env('JORSID_BUSINESS_NAME', 'Jorsid Beauty by Kessy'),
    'admin_name' => env('JORSID_ADMIN_NAME', 'Kessy'),
    'admin_language' => env('JORSID_ADMIN_LANGUAGE', 'de'),
    'home_travel_buffer_minutes' => (int) env('JORSID_HOME_TRAVEL_BUFFER_MINUTES', 30),
    'translation_endpoint' => env('JORSID_TRANSLATION_URL', ''),
    'translation_api_key' => env('JORSID_TRANSLATION_API_KEY', ''),
];
