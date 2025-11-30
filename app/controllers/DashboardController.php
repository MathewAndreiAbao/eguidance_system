<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class DashboardController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('AppointmentModel');
        $this->call->model('UserModel');
        $this->call->model('ProfileModel');
        $this->call->model('ResourceModel');
        $this->call->library('session');
        $this->call->library('Auth');
        $this->call->library('pagination');
        $this->call->library('APIIntegration');
        
        if (!$this->Auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    public function student($page = 1) {
        if ($this->session->userdata('role') != 'student') {
            redirect('counselor/dashboard');
        }
        
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        
        // Get upcoming appointments for the next 30 days
        $upcoming_appointments = $this->AppointmentModel->getStudentAppointments($user_id);
        $filtered_appointments = array_filter($upcoming_appointments, function($apt) {
            $apt_date = strtotime($apt['date']);
            $now = time();
            $future_limit = strtotime('+30 days');
            return $apt_date >= $now && $apt_date <= $future_limit;
        });
        
        // Sort appointments by date
        usort($filtered_appointments, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        // Get today's appointments
        $todays_appointments = array_filter($upcoming_appointments, function($apt) {
            return $apt['date'] == date('Y-m-d');
        });
        
        // Get pending appointments
        $pending_appointments = array_filter($upcoming_appointments, function($apt) {
            return $apt['status'] == 'pending';
        });
        
        // Get motivational quote using ZenQuotes API
        $motivational_quote = $this->APIIntegration->get_inspirational_quote();
        
        // Pagination for resources
        $current_page = (int) segment(3) ?: 1;
        $total_resources = $this->ResourceModel->count_all();
        $rows_per_page = 6;
        $base_url = 'student/dashboard';
        
        $page_data = $this->pagination->initialize(
            $total_resources,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $limit_clause = $page_data['limit'];
        $paginated_resources = $this->ResourceModel->get_paginated($limit_clause);
        
        $data = [
            'profile' => $this->ProfileModel->getProfileWithUser($user_id),
            'appointments' => array_slice($filtered_appointments, 0, 5), // Limit to 5 upcoming appointments
            'todays_appointments' => $todays_appointments,
            'pending_appointments' => $pending_appointments,
            'counselors' => $this->UserModel->getAllCounselors(),
            'recent_resources' => $paginated_resources,
            'recent_announcements' => [],
            'role' => $role,
            'pagination' => $this->pagination->paginate(),
            'motivational_quote' => $motivational_quote
        ];
        
        $this->call->view('auth/dashboard', $data);
    }

    public function counselor($page = 1) {
        if ($this->session->userdata('role') != 'counselor') {
            redirect('student/dashboard');
        }
        
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        
        // Get today's appointments
        $todays_appointments = array_filter($this->AppointmentModel->getAllAppointments(), function($apt) {
            return $apt['date'] == date('Y-m-d');
        });
        
        // Get pending appointments
        $pending_appointments = array_filter($this->AppointmentModel->getAllAppointments(), function($apt) {
            return $apt['status'] == 'pending';
        });
        
        // Sort pending appointments by date
        usort($pending_appointments, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        // Get motivational quote using ZenQuotes API
        $motivational_quote = $this->APIIntegration->get_inspirational_quote();
        
        // Pagination for resources
        $current_page = (int) segment(3) ?: 1;
        $total_resources = $this->ResourceModel->count_by_counselor($user_id);
        $rows_per_page = 6;
        $base_url = 'counselor/dashboard';
        
        $page_data = $this->pagination->initialize(
            $total_resources,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $limit_clause = $page_data['limit'];
        $paginated_resources = $this->ResourceModel->get_paginated_by_counselor($user_id, $limit_clause);
        
        $data = [
            'profile' => $this->ProfileModel->getProfileWithUser($user_id),
            'all_appointments' => $this->AppointmentModel->getAllAppointments(),
            'todays_appointments' => $todays_appointments,
            'pending_appointments' => array_slice($pending_appointments, 0, 5), // Limit to 5 pending appointments
            'my_announcements' => [],
            'my_resources' => $paginated_resources,
            'recent_resources' => $this->ResourceModel->get_recent(5), // Get only 5 most recent resources
            'role' => $role,
            'pagination' => $this->pagination->paginate(),
            'motivational_quote' => $motivational_quote
        ];
        
        $this->call->view('auth/dashboard', $data);
    }
}