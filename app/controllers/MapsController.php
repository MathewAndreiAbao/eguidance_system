<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Maps Controller - Campus Map Integration
 * 
 * Features:
 * - Interactive campus map using OpenStreetMap and Leaflet.js
 * - Location markers for key campus buildings
 * - Distance measurement tools
 * - Responsive design for all devices
 */
class MapsController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->library('auth');
        $this->call->library('session');

        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    public function index() {
        // Load map configuration
        $map_config = require_once APP_DIR . 'config/maps.php';
        // Pass configuration to view
        $data['map_config'] = $map_config;
        // Load the campus map view with data
        $this->call->view('maps/campus', $data);
    }
    
    public function campus() {
        // Load map configuration
        $map_config = require_once APP_DIR . 'config/maps.php';
        // Pass configuration to view
        $data['map_config'] = $map_config;
        // Load the campus map view with data
        $this->call->view('maps/campus', $data);
    }
}