<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class DashboardController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('AppointmentModel');
        $this->call->model('UserModel');
        $this->call->model('ProfileModel');
        $this->call->library('session');
        $this->call->library('Auth');
        
        // Check authentication for all methods
        if (!$this->Auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    public function student() {
        if ($this->session->userdata('role') != 'student') {
            redirect('counselor/dashboard');
        }
        
        $user_id = $this->session->userdata('user_id');
        
        $data = [
            'profile' => $this->ProfileModel->getProfileWithUser($user_id),
            'appointments' => $this->AppointmentModel->getStudentAppointments($user_id),
            'counselors' => $this->UserModel->getAllCounselors()
        ];
        
        $this->call->view('auth/dashboard', $data);
    }

    public function counselor() {
        if ($this->session->userdata('role') != 'counselor') {
            redirect('student/dashboard');
        }
        
        $user_id = $this->session->userdata('user_id');
        
        $data = [
            'profile' => $this->ProfileModel->getProfileWithUser($user_id),
            'all_appointments' => $this->AppointmentModel->getAllAppointments(),
            'pending_appointments' => array_filter($this->AppointmentModel->getAllAppointments(), function($appointment) {
                return $appointment['status'] == 'pending';
            })
        ];
        
        $this->call->view('auth/dashboard', $data);
    }
}