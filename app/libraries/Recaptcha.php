<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Recaptcha {
    private $siteKey;
    private $secretKey;
    private $version;
    
    public function __construct() {
        // Skip reCAPTCHA in development environment
        if (config_item('ENVIRONMENT') === 'development') {
            $this->siteKey = '';
            $this->secretKey = '';
            $this->version = 'v2';
            return;
        }
        
        $this->siteKey = config_item('recaptcha_site_key');
        $this->secretKey = config_item('recaptcha_secret_key');
        $this->version = config_item('recaptcha_version') ?: 'v2';
    }
    
    /**
     * Verify reCAPTCHA response
     * 
     * @param string $recaptchaResponse
     * @return bool
     */
    public function verify($recaptchaResponse) {
        // Skip verification if keys are not set (development mode)
        if (empty($this->secretKey) || $this->secretKey === 'YOUR_RECAPTCHA_SECRET_KEY') {
            return true;
        }
        
        if (empty($recaptchaResponse)) {
            return false;
        }
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->secretKey,
            'response' => $recaptchaResponse
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result);
        
        // Log the response for debugging
        error_log('reCAPTCHA response: ' . print_r($response, true));
        
        // Check for errors in the response
        if (!empty($response->{'error-codes'})) {
            error_log('reCAPTCHA errors: ' . implode(', ', $response->{'error-codes'}));
        }
        
        // For reCAPTCHA v3, check the score
        if ($this->version === 'v3' && isset($response->score)) {
            // Generally, scores >= 0.5 are considered human
            return isset($response->success) && $response->success && $response->score >= 0.5;
        }
        
        return isset($response->success) && $response->success;
    }
    
    /**
     * Get reCAPTCHA site key
     * 
     * @return string
     */
    public function getSiteKey() {
        return $this->siteKey;
    }
    
    /**
     * Get reCAPTCHA version
     * 
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }
    
    /**
     * Check if reCAPTCHA is configured
     * 
     * @return bool
     */
    public function isConfigured() {
        return !empty($this->siteKey) && 
               !empty($this->secretKey) && 
               $this->siteKey !== 'YOUR_RECAPTCHA_SITE_KEY' && 
               $this->secretKey !== 'YOUR_RECAPTCHA_SECRET_KEY';
    }
}