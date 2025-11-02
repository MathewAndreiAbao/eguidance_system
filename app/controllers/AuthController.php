<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->model('UserModel');
        $this->call->model('ProfileModel');
        $this->call->library('auth');
    }

    public function login() {
        if ($this->auth->is_logged_in()) {
            $this->redirectBasedOnRole();
        }

        if ($_POST) {
            $username = filter_io('string', $this->io->post('username'));
            $password = $this->io->post('password');

            if ($this->auth->login($username, $password)) {
                $this->redirectBasedOnRole();
            } else {
                $this->session->set_flashdata('error', 'Invalid username or password');
            }
        }

        $this->call->view('auth/login');
    }

    public function register() {
        if ($this->auth->is_logged_in()) {
            $this->redirectBasedOnRole();
        }

        if ($_POST) {
            $username = filter_io('string', $this->io->post('username'));
            $password = $this->io->post('password');
            $confirm_password = $this->io->post('confirm_password');
            $role = filter_io('string', $this->io->post('role'));

            if ($password === $confirm_password && in_array($role, ['student', 'counselor'])) {
                if (!$this->UserModel->username_exists($username)) {
                    $user_id = $this->UserModel->register($username, $password, $role);

                    // Create initial profile
                    $profile_data = [
                        'user_id' => $user_id,
                        'name' => $username,
                        'email' => '',
                        'phone' => '',
                        'bio' => ''
                    ];
                    $this->ProfileModel->create_record($profile_data);

                    $this->session->set_flashdata('success', 'Registration successful! Please login.');
                    redirect('auth/login');
                } else {
                    $this->session->set_flashdata('error', 'Username already exists');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid input data');
            }
        }

        $this->call->view('auth/register');
    }

    public function logout() {
        $this->auth->logout();
        redirect('auth/login');
    }

    private function redirectBasedOnRole() {
        $role = $this->session->userdata('role');
        switch ($role) {
            case 'student':
                redirect('student/dashboard');
                break;
            case 'counselor':
                redirect('counselor/dashboard');
                break;
            default:
                redirect('auth/login');
        }
    }
}
