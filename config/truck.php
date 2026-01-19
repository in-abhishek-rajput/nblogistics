<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Truck Statuses
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible statuses for trucks.
    | These statuses are used throughout the application for filtering,
    | displaying, and managing truck availability.
    |
    */

     'statuses' => [
        'available' => [
            'label' => 'Available',
            'color' => 'success', // Bootstrap color class
            'icon' => 'bi-check-circle', // Bootstrap icon
        ],
        'not_available' => [
            'label' => 'Not Available',
            'color' => 'danger',
            'icon' => 'bi-x-circle',
        ],
        'hold' => [
            'label' => 'Hold',
            'color' => 'warning',
            'icon' => 'bi-pause-circle',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Truck Types
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible types for trucks.
    | These types are used for categorization and filtering.
    |
    */
    'types' => [
        'mini-truck' => 'Mini Truck/LCV',
        'open-truck' => 'Open Body Truck',
        'closed-container' => 'Closed Container',
        'trailer' => 'Trailer',
        'tanker' => 'Tanker',
        'tipper' => 'Tipper',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ownership Options
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible ownership types for trucks.
    |
    */
    'ownerships' => [
        'self' => 'Self',
        'market' => 'Market',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Status
    |--------------------------------------------------------------------------
    |
    | The default status assigned to new trucks.
    |
    */
    'default_status' => 'available',
];