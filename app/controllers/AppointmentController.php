<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AppointmentController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->model('AppointmentModel');
        $this->call->model('UserModel');
        $this->call->library('auth');
    }

    public function index() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        if ($role == 'student') {
            $data['appointments'] = $this->AppointmentModel->get_student_appointments($user_id);
        } else {
            $data['appointments'] = $this->AppointmentModel->get_all();
        }

        $this->call->view('appointments/index', $data);
    }

    public function create() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') != 'student') {
            $this->session->set_flashdata('error', 'Only students can create appointments');
            redirect('appointments');
        }

        if ($_POST) {
            $data = [
                'student_id' => $this->session->userdata('user_id'),
                'date' => filter_io('string', $this->io->post('date')),
                'time' => filter_io('string', $this->io->post('time')),
                'purpose' => filter_io('string', $this->io->post('purpose')),
                'counselor_id' => filter_io('string', $this->io->post('counselor_id')),
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->AppointmentModel->is_time_slot_available($data['date'], $data['time'], $data['counselor_id'])) {
                $this->AppointmentModel->create_record($data);
                $this->session->set_flashdata('success', 'Appointment created successfully');
                redirect('appointments');
            } else {
                $this->session->set_flashdata('error', 'Time slot is not available');
            }
        } else {
            $data['counselors'] = $this->UserModel->get_all_counselors();
            $this->call->view('appointments/create', $data);
        }
    }

    public function edit($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $appointment = $this->AppointmentModel->get_by_id($id);

        if (!$appointment) {
            $this->session->set_flashdata('error', 'Appointment not found');
            redirect('appointments');
        }

        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        // Check authorization
        if ($role == 'student' && $appointment['student_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('appointments');
        }

        if ($_POST) {
            if ($role == 'student') {
                $data = [
                    'date' => filter_io('string', $this->io->post('date')),
                    'time' => filter_io('string', $this->io->post('time')),
                    'purpose' => filter_io('string', $this->io->post('purpose'))
                ];
            } elseif ($role == 'counselor') {
                $data = [
                    'status' => filter_io('string', $this->io->post('status'))
                ];
            }

            $this->AppointmentModel->update_record($id, $data);
            $this->session->set_flashdata('success', 'Appointment updated successfully');

            if ($role == 'counselor') {
                redirect('counselor/dashboard');
            } else {
                redirect('appointments');
            }
        } else {
            $data['appointment'] = $appointment;
            $data['counselors'] = $this->UserModel->get_all_counselors();
            $this->call->view('appointments/edit', $data);
        }
    }

    public function delete($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $appointment = $this->AppointmentModel->get_by_id($id);

        if (!$appointment) {
            $this->session->set_flashdata('error', 'Appointment not found');
            redirect('appointments');
        }

        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        // Check authorization
        if ($role == 'student' && $appointment['student_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('appointments');
        }

        $this->AppointmentModel->delete_record($id);
        $this->session->set_flashdata('success', 'Appointment deleted successfully');
        redirect('appointments');
    }

    public function upcoming() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        $data['appointments'] = $this->AppointmentModel->get_upcoming_appointments($user_id, $role);
        $this->call->view('appointments/upcoming', $data);
    }
}
