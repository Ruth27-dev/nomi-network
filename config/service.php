<?php
    return [
        'google_map'    => [
            'api_key'               => env('GOOGLE_MAPS_API_KEY', ''),
        ],
        'firebase'      => [
            'api_key'               => env('FIREBASE_API_KEY', ''),
            'auth_domain'           => env('FIREBASE_AUTH_DOMAIN'),
            'project_id'            => env('FIREBASE_PROJECT_ID'),
            'storage_bucket'        => env('FIREBASE_STORAGE_BUCKET'),
            'messaging_sender_id'   => env('FIREBASE_MESSAGING_SENDER_ID'),
        ],
    ];
?>
