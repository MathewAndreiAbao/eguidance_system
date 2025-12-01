<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Maps Configuration
 * Contains map settings and API configurations
 */

return [
    'default_zoom' => 15,
    'campus_center' => [
        'lat' => 13.4108,
        'lng' => 121.1797
    ],
    'map_provider' => 'openstreetmap',
    'api_keys' => [
        'google_maps' => '',
        'mapbox' => ''
    ],
    'markers' => [
        'administration' => [
            'name' => 'Administration Building',
            'lat' => 13.4108,
            'lng' => 121.1797,
            'icon' => 'building'
        ],
        'library' => [
            'name' => 'Library',
            'lat' => 13.4105,
            'lng' => 121.1795,
            'icon' => 'book'
        ],
        'cafeteria' => [
            'name' => 'Cafeteria',
            'lat' => 13.4110,
            'lng' => 121.1799,
            'icon' => 'utensils'
        ]
    ]
];
?>