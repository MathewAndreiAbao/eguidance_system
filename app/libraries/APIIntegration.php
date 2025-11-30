<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * API Integration Library
 * Handles Calendarific, Groq AI, and ZenQuotes APIs
 */
class APIIntegration {
    
    private $config;

    public function __construct() {
        // Load analytics configuration
        $config_file = dirname(__FILE__) . '/../config/analytics.php';
        if (file_exists($config_file)) {
            $this->config = require $config_file;
        }
    }

    /**
     * Format phone number to E.164 format
     * @param string $phone Phone number
     * @return string Formatted phone number or empty string
     */
    private function format_phone_number($phone) {
        if (empty($phone)) {
            return '';
        }
        
        // Remove all non-digit characters except +
        $clean = preg_replace('/[^0-9+]/', '', $phone);
        
        // If it starts with +, validate it's a proper E.164 format for Philippines (+63 followed by 10 digits)
        if (strpos($clean, '+') === 0) {
            // Check if it's a valid Philippine number format: +63XXXXXXXXXX (12 digits total)
            if (preg_match('/^\+63[0-9]{10}$/', $clean)) {
                return $clean;
            }
            return '';
        }
        
        // Handle Philippine numbers
        // 09123456789 (10 digits starting with 0) -> +639123456789
        if (strlen($clean) === 10 && substr($clean, 0, 1) === '0') {
            return '+63' . substr($clean, 1);
        }
        
        // 9123456789 (9 digits) -> +639123456789
        if (strlen($clean) === 9) {
            return '+63' . $clean;
        }
        
        // 639123456789 (11 digits starting with 63) -> +639123456789
        if (strlen($clean) === 11 && substr($clean, 0, 2) === '63') {
            return '+63' . substr($clean, 2);
        }
        
        // 6309123456789 (12 digits starting with 630) -> +639123456789
        if (strlen($clean) === 12 && substr($clean, 0, 3) === '630') {
            return '+63' . substr($clean, 3);
        }
        
        // For other cases, if it's 10 digits and doesn't start with 0, assume it's missing +63
        if (strlen($clean) === 10 && substr($clean, 0, 1) !== '0') {
            return '+63' . $clean;
        }
        
        // Return empty string for unrecognizable formats
        return '';
    }

    // ============================================
    // ZENQUOTES API - Inspirational Quotes
    // ============================================
    
    /**
     * Get a random inspirational quote from ZenQuotes API
     * @return string Quote or empty string
     */
    public function get_inspirational_quote() {
        // ZenQuotes API endpoint for random quotes
        $url = ($this->config['endpoints']['zenquotes'] ?? 'https://zenquotes.io/api') . '/random';
        
        // Try to get from session cache first (1 hour cache)
        $cached_quote = $_SESSION['zenquote_cache'] ?? null;
        $cache_time = $_SESSION['zenquote_cache_time'] ?? 0;
        
        if ($cached_quote && (time() - $cache_time) < 3600) { // 1 hour cache
            return $cached_quote;
        }
        
        // Make the HTTP request
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'E-Guidance System',
            CURLOPT_FOLLOWLOCATION => true
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response && $http_code === 200) {
            $data = json_decode($response, true);
            
            // ZenQuotes returns an array with one quote object
            if (!empty($data) && is_array($data) && isset($data[0]['q'])) {
                $quote = $data[0]['q'] . ' - ' . $data[0]['a'];
                
                // Cache the quote in session
                $_SESSION['zenquote_cache'] = $quote;
                $_SESSION['zenquote_cache_time'] = time();
                
                return $quote;
            }
        }
        
        // Return empty string if API fails
        return '';
    }
    
    // ============================================
    // CALENDARIFIC API - Holidays
    // ============================================
    
    /**
     * Get holidays for current year (Philippines)
     * @param int $year Year (default: current year)
     * @return array Holidays or empty array
     */
    public function get_holidays($year = null) {
        $api_key = $this->config['api_keys']['calendarific'] ?? '';
        
        if (empty($api_key)) {
            return [];
        }

        $year = $year ?? date('Y');
        $url = ($this->config['endpoints']['calendarific'] ?? 'https://calendarific.com/api/v2/holidays') . "?api_key={$api_key}&country=PH&year={$year}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        unset($ch);

        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['response']['holidays'])) {
                return $data['response']['holidays'];
            }
        }

        return [];
    }

    /**
     * Check if a date is a holiday
     * @param string $date Date in Y-m-d format
     * @return bool|string False or holiday name
     */
    public function is_holiday($date) {
        $holidays = $this->get_holidays(date('Y', strtotime($date)));
        
        foreach ($holidays as $holiday) {
            if ($holiday['date']['iso'] === $date) {
                return $holiday['name'];
            }
        }

        return false;
    }

    /**
     * Get upcoming holidays (next 30 days)
     * @return array
     */
    public function get_upcoming_holidays($days = 30) {
        $holidays = $this->get_holidays();
        $upcoming = [];
        $today = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime("+{$days} days"));

        foreach ($holidays as $holiday) {
            $holiday_date = $holiday['date']['iso'];
            if ($holiday_date >= $today && $holiday_date <= $end_date) {
                $upcoming[] = $holiday;
            }
        }

        return $upcoming;
    }

    // ============================================
    // GROQ AI API - Chatbot
    // ============================================
    
    /**
     * Get AI response using Groq AI
     * @param string $message User message
     * @param array $conversationHistory Previous conversation messages
     * @return string AI response or fallback response
     */
    public function get_ai_response($message, $conversationHistory = []) {
        // Get API key and endpoint
        $api_key = $this->config['api_keys']['groq_api_key'] ?? '';
        $endpoint = $this->config['endpoints']['groq'] ?? 'https://api.groq.com/openai/v1/chat/completions';
        
        // If no API key, return error message
        if (empty($api_key)) {
            return 'Sorry, I am currently unable to process your request. Please try again later.';
        }
        
        // Prepare messages array
        $messages = [];
        
        // Add system message for context
        $messages[] = [
            'role' => 'system',
            'content' => 'You are a helpful counseling assistant for a student guidance system. Provide supportive, informative, and professional responses. Be empathetic and encouraging.'
        ];
        
        // Add conversation history (limit to last 4 exchanges to keep context manageable)
        $recentHistory = array_slice($conversationHistory, -4);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        
        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];
        
        // Prepare request data
        $data = [
            'messages' => $messages,
            'model' => 'openai/gpt-oss-20b',
            'temperature' => 1,
            'max_completion_tokens' => 8192,
            'top_p' => 1,
            'stream' => false,
            'reasoning_effort' => 'medium',
            'stop' => null
        ];
        
        // Make the HTTP request
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        // curl_close($ch); // Deprecated - using curl_init without explicit close
        
        // Log for debugging - including endpoint and key info (first 5 chars)
        error_log("Groq AI Request to: {$endpoint} with key: " . substr($api_key, 0, 5) . "...");
        error_log("Groq AI Response: HTTP {$http_code}, Response: {$response}, Error: {$error}");
        
        // Handle response
        if ($response && $http_code === 200) {
            $result = json_decode($response, true);
            if (!empty($result['choices'][0]['message']['content'])) {
                return trim($result['choices'][0]['message']['content']);
            }
        }
        
        // If API call fails, return error message
        return 'Sorry, I am currently unable to process your request. Please try again later.';
    }
    

}