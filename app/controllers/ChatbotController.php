<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ChatbotController extends Controller {
    
    private $config;
    
    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->library('Auth');
        $this->call->library('APIIntegration');
        $this->call->model('AppointmentModel');
        
        // Load analytics configuration for API keys
        $config_file = APP_DIR . 'config/analytics.php';
        if (file_exists($config_file)) {
            $this->config = require $config_file;
        }
        
        if (!$this->Auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    public function chat() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');
        $conversationHistory = $input['conversationHistory'] ?? [];
        
        if (empty($message)) {
            echo json_encode(['response' => 'Please provide a message.']);
            exit;
        }
        
        // All questions go directly to Groq AI API
        $aiResponse = $this->APIIntegration->get_ai_response($message, $conversationHistory);
        echo json_encode(['response' => $aiResponse]);
        exit;
    }
    
    /**
     * Get AI response using Groq AI API
     */
    private function getAIResponse($message, $conversationHistory = []) {
        // Use the APIIntegration library method
        return $this->APIIntegration->get_ai_response($message, $conversationHistory);
    }
}

