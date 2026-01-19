<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver Statuses
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible statuses for drivers.
    | These statuses are used throughout the application for filtering,
    | displaying, and managing driver availability.
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
    | Default Status
    |--------------------------------------------------------------------------
    |
    | The default status assigned to new drivers.
    |
    */
    'default_status' => 'available',
];