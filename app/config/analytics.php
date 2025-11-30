<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Analytics API Configuration
 * 
 * This file contains configuration for external analytics integrations.
 */
return [
    // Enable/Disable external API integrations
    'enabled' => true,

    // API Keys for external services (Keep these secure!)
    'api_keys' => [
        // Calendarific API Integration
        'calendarific' => getenv('CALENDARIFIC_API_KEY') ?: 'u4nTWWaO6BhGBABL0AW0ax4ugprtPiiN',
        // Groq AI Chatbot API
        'groq_api_key' => getenv('GROQ_API_KEY') ?: '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',
        // ZenQuotes API (no key required for basic usage)
        'zenquotes' => getenv('ZENQUOTES_API_KEY') ?: '',
    ],

    // API Endpoints for external services
    'endpoints' => [
        // Calendarific API endpoint
        'calendarific' => 'https://calendarific.com/api/v2/holidays',
        // Groq AI API endpoint
        'groq' => 'https://api.groq.com/openai/v1/chat/completions',
        // ZenQuotes API endpoint
        'zenquotes' => 'https://zenquotes.io/api',
    ],

    // Internal API Settings
    'internal_api' => [
        'rate_limit' => 100, // requests per hour
        'require_auth' => true,
        'allowed_origins' => ['*'], // CORS settings
    ],
];