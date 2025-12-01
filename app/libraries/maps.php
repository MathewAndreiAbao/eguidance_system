<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Map Configuration
 * 
 * This file contains configuration for map integrations.
 */
return [
    // Default coordinates for Guidance and Counseling Office
    'default_lat' => getenv('MAP_DEFAULT_LAT') ?: 13.388380,
    'default_lng' => getenv('MAP_DEFAULT_LNG') ?: 121.162682,
    'default_zoom' => getenv('MAP_DEFAULT_ZOOM') ?: 19,
    
    // Map tile provider
    'tile_provider' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
];