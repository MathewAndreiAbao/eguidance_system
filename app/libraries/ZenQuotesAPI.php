<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * ZenQuotes API Integration Library
 * Handles inspirational quotes from ZenQuotes API
 */
class ZenQuotesAPI {
    
    private $config;

    public function __construct() {
        // Configuration is now in config.php
    }

    /**
     * Get a random inspirational quote from ZenQuotes API
     * @return string Quote or empty string
     */
    public function get_inspirational_quote() {
        // ZenQuotes API endpoint for random quotes
        $endpoint = config_item('zenquotes_api_endpoint');
        $url = "{$endpoint}/random";
        
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
        // curl_close($ch); // Deprecated in PHP 8.0+
        
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
}
?>