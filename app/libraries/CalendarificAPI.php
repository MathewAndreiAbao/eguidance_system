<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Calendarific API Integration Library
 * Handles holiday data from Calendarific API
 */
class CalendarificAPI {
    
    private $config;

    public function __construct() {
        // Configuration is now in config.php
        $this->config = [
            'api_keys' => [
                'calendarific' => config_item('calendarific_api_key')
            ],
            'endpoints' => [
                'calendarific' => config_item('calendarific_api_endpoint')
            ]
        ];
    }

    /**
     * Get holidays for current year (Philippines)
     * @param int $year Year (default: current year)
     * @return array Holidays or empty array
     */
    public function get_holidays($year = null) {
        $api_key = config_item('calendarific_api_key');
        
        if (empty($api_key)) {
            return [];
        }

        $year = $year ?? date('Y');
        $endpoint = config_item('calendarific_api_endpoint');
        $url = "{$endpoint}?api_key={$api_key}&country=PH&year={$year}";

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
}
?>