<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * API Test Controller
 * Provides testing endpoints for external API integrations
 */
class APITestController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->library('auth');
        $this->call->library('session');
        
        // Only counselors can access API test dashboard
        if (!$this->auth->is_logged_in() || $this->session->userdata('role') !== 'counselor') {
            redirect('auth/login');
        }
    }

    /**
     * Display API test dashboard
     */
    public function index() {
        $data = [
            'page_title' => 'API Test Dashboard',
            'api_config' => [
                'calendarific' => config_item('calendarific_api_key'),
                'groq' => config_item('groq_api_key'),
                'zenquotes' => config_item('zenquotes_api_key')
            ]
        ];
        
        $this->call->view('api_test', $data);
    }

    /**
     * Test Calendarific API
     */
    public function test_calendarific() {
        $this->call->library('CalendarificAPI');
        
        // Get holidays for current year
        $holidays = $this->CalendarificAPI->get_holidays();
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => array_slice($holidays, 0, 5), // Return first 5 holidays
            'count' => count($holidays)
        ]);
    }

    /**
     * Test Groq AI API
     */
    public function test_groq() {
        $this->call->library('GroqAIAPI');
        
        // Test with a simple message
        $test_message = "Hello, this is a test message.";
        $response = $this->GroqAIAPI->get_ai_response($test_message);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => $test_message,
            'response' => $response
        ]);
    }

    /**
     * Test ZenQuotes API
     */
    public function test_zenquotes() {
        $this->call->library('ZenQuotesAPI');
        
        // Get an inspirational quote
        $quote = $this->ZenQuotesAPI->get_inspirational_quote();
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'quote' => $quote
        ]);
    }

    /**
     * Test holiday check functionality
     */
    public function test_holiday_check() {
        $this->call->library('CalendarificAPI');
        
        // Check if today is a holiday
        $today = date('Y-m-d');
        $is_holiday = $this->CalendarificAPI->is_holiday($today);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'date' => $today,
            'is_holiday' => $is_holiday ? true : false,
            'holiday_name' => $is_holiday ? $is_holiday : null
        ]);
    }
}
?>