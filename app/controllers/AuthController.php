<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('UserModel');
        $this->call->model('ProfileModel');
        $this->call->library('auth');
        $this->call->library('recaptcha');
    }

    public function index() {
        if ($this->auth->is_logged_in()) {
            $this->redirectBasedOnRole();
        }
        redirect('auth/login');
    }

    public function login() {
        // Redirect if already logged in
        if ($this->auth->is_logged_in()) {
            $this->redirectBasedOnRole();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('username')->required();
            $this->form_validation->name('password')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                // Verify reCAPTCHA (skip in development mode)
                if (config_item('ENVIRONMENT') !== 'development') {
                    if (!isset($_POST['g-recaptcha-response']) || !$this->recaptcha->verify($_POST['g-recaptcha-response'])) {
                        $this->session->set_flashdata('error', 'Please complete the reCAPTCHA verification');
                        redirect('auth/login');
                        return;
                    }
                }
                
                // Get and validate credentials
                $username = filter_io('string', $this->io->post('username'));
                $password = $this->io->post('password');

                if (empty($username) || empty($password)) {
                    $this->session->set_flashdata('error', 'Please provide both username and password');
                    redirect('auth/login');
                    return;
                }

                // Verify credentials
                if ($this->UserModel->verify_password($username, $password)) {
                    $user = $this->UserModel->get_user_by_username($username);
                    if (!$user) {
                        $this->session->set_flashdata('error', 'User account not found');
                        redirect('auth/login');
                        return;
                    }

                    // Get user's email from profile
                    $user_id = $user['id'];
                    $profile = $this->ProfileModel->get_profile_with_user($user_id);
                        
                    // Set authenticated session directly without OTP verification
                    $this->session->set_userdata([
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role'],
                        'logged_in' => true
                    ]);
                    
                    // Success message and redirect
                    $this->session->set_flashdata('success', 'Login successful! Welcome back.');
                    $this->redirectBasedOnRole();
                    return;
                } else {
                    $this->session->set_flashdata('error', 'Invalid username or password');
                    redirect('auth/login');
                    return;
                }
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                $this->call->view('auth/login');
                return;
            }
        }

        // Display login form
        $this->call->view('auth/login');
    }

    public function verify_otp() {
        // Redirect to login as OTP is no longer used
        $this->session->set_flashdata('error', 'Invalid request.');
        redirect('auth/login');
    }

    public function resend_otp() {
        // Redirect to login as OTP is no longer used
        $this->session->set_flashdata('error', 'Invalid request.');
        redirect('auth/login');
    }

    public function register() {
        if ($this->auth->is_logged_in()) {
            $this->redirectBasedOnRole();
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('username')->required()->alpha_numeric();
            $this->form_validation->name('password')->required()->min_length(6);
            $this->form_validation->name('confirm_password')->required();
            $this->form_validation->name('email')->required()->valid_email();
            $this->form_validation->name('role')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                // Verify reCAPTCHA (skip in development mode)
                if (config_item('ENVIRONMENT') !== 'development') {
                    if (!isset($_POST['g-recaptcha-response']) || !$this->recaptcha->verify($_POST['g-recaptcha-response'])) {
                        $this->session->set_flashdata('error', 'Please complete the reCAPTCHA verification');
                        $this->call->view('auth/register');
                        return;
                    }
                }
                
                $username = filter_io('string', $this->io->post('username'));
                $password = $this->io->post('password');
                $confirm_password = $this->io->post('confirm_password');
                $role = filter_io('string', $this->io->post('role'));

                $email = filter_io('string', $this->io->post('email'));
                
                if ($password === $confirm_password && in_array($role, ['student', 'counselor'])) {
                    if (!$this->UserModel->username_exists($username)) {
                        // Validate email format
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $this->session->set_flashdata('error', 'Please enter a valid email address');
                            $this->call->view('auth/register');
                            return;
                        }

                        $user_id = $this->UserModel->register($username, $password, $role);

                        $profile_data = [
                            'user_id' => $user_id,
                            'name' => $username,
                            'email' => $email,
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
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                $this->call->view('auth/register');
                return;
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

    // Test method removed as SMTP is no longer used
    public function test_email() {
        // Redirect to login as email testing is no longer used
        $this->session->set_flashdata('error', 'Invalid request.');
        redirect('auth/login');
    }
}