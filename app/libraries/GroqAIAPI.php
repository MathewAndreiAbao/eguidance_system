<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Groq AI API Integration Library
 * Handles AI chatbot responses using Groq AI API
 */
class GroqAIAPI {
    
    private $config;

    public function __construct() {
        // Configuration is now in config.php
    }

    /**
     * Get AI response using Groq AI
     * @param string $message User message
     * @param array $conversationHistory Previous conversation messages
     * @return string AI response or fallback response
     */
    public function get_ai_response($message, $conversationHistory = []) {
        // Get API key and endpoint
        $api_key = config_item('groq_api_key');
        $endpoint = config_item('groq_api_endpoint');
        
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
?>