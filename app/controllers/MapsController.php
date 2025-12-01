<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Maps Controller
 * Handles campus map integration and location services
 */
class MapsController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->library('auth');
    }

    /**
     * Display main maps page
     */
    public function index() {
        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
        }
        
        $this->call->view('maps/index');
    }

    /**
     * Display campus map
     */
    public function campus() {
        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
        }
        
        $data = [
            'page_title' => 'Campus Map',
            'map_type' => 'campus'
        ];
        
        $this->call->view('maps/campus', $data);
    }
}
?>