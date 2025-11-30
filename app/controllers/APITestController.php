<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * API Test Controller
 * Used to test all API integrations
 */
class APITestController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->library('APIIntegration');
        $this->call->library('session');
        $this->call->library('Auth');
        
        if (!$this->Auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    public function index() {
        $this->call->view('api_test');
    }
    
    public function test_calendarific() {
        header('Content-Type: application/json');
        
        $year = date('Y');
        $holidays = $this->APIIntegration->get_holidays($year);
        
        if (!empty($holidays)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Calendarific API is working',
                'holidays_count' => count($holidays),
                'sample_holidays' => array_slice($holidays, 0, 3)
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch holidays from Calendarific API'
            ]);
        }
    }
    
    public function test_groq() {
        header('Content-Type: application/json');
        
        $test_message = "Hello, how are you?";
        $response = $this->APIIntegration->get_ai_response($test_message);
        
        if (!empty($response)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Groq AI API is working',
                'test_response' => $response
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get response from Groq AI API'
            ]);
        }
    }
    
    public function test_holiday_check() {
        header('Content-Type: application/json');
        
        $today = date('Y-m-d');
        $is_holiday = $this->APIIntegration->is_holiday($today);
        
        if ($is_holiday !== false) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Holiday checking is working',
                'is_holiday' => true,
                'holiday_name' => $is_holiday,
                'date' => $today
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Holiday checking is working',
                'is_holiday' => false,
                'date' => $today
            ]);
        }
    }
}