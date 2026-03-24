<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trip Statuses
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible statuses for trips.
    | These statuses are used throughout the application for filtering,
    | displaying, and managing trip availability.
    |
    */

    'statuses' => [
        'pending' => [
            'label' => 'Pending',
            'color' => 'warning', // Bootstrap color class
            'icon' => 'bi-clock', // Bootstrap icon
        ],
        'start' => [
            'label' => 'Start',
            'color' => 'primary',
            'icon' => 'bi-play-circle',
        ],
        'completed' => [
            'label' => 'Completed',
            'color' => 'success',
            'icon' => 'bi-check-circle',
        ],
        'pod_received' => [
            'label' => 'POD Received',
            'color' => 'info',
            'icon' => 'bi-file-earmark-text',
        ],
        'pod_submitted' => [
            'label' => 'POD Submitted',
            'color' => 'secondary',
            'icon' => 'bi-file-earmark-check',
        ],
        'settled' => [
            'label' => 'Settled',
            'color' => 'dark',
            'icon' => 'bi-check-circle-fill',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Types
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible billing types for trips.
    | These are used for freight calculations.
    |
    */
    'billing_types' => [
        'fixed' => 'Fixed',
        'per_tonne' => 'Per Tonne',
        'per_kg' => 'Per Kg',
        'per_km' => 'Per Km',
        'per_trip' => 'Per Trip',
        'per_day' => 'Per Day',
        'per_hour' => 'Per Hour',
        'per_litre' => 'Per Litre',
        'per_bag' => 'Per Bag',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Status
    |--------------------------------------------------------------------------
    |
    | The default status assigned to new trips.
    |
    */
    'default_status' => 'pending',
];