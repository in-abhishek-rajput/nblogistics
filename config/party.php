<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Party Statuses
    |--------------------------------------------------------------------------
    |
    | This configuration defines the possible statuses for parties.
    | These statuses are used throughout the application for filtering,
    | displaying, and managing party availability.
    |
    */

    'statuses' => [
        'active' => [
            'label' => 'Active',
            'color' => 'success', // Bootstrap color class
            'icon' => 'bi-check-circle', // Bootstrap icon
        ],
        'inactive' => [
            'label' => 'Inactive',
            'color' => 'danger',
            'icon' => 'bi-x-circle',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Status
    |--------------------------------------------------------------------------
    |
    | The default status assigned to new parties.
    |
    */
    'default_status' => 'active',
];