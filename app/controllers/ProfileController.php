<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProfileController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('ProfileModel');
        $this->call->model('UserModel');
        $this->call->library('auth');
    }

    public function index() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $user_id = $this->session->userdata('user_id');
        $data['profile'] = $this->ProfileModel->get_profile_with_user($user_id);
        $this->call->view('profile/index', $data);
    }

    public function edit() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $user_id = $this->session->userdata('user_id');

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('name')->required();
            $this->form_validation->name('email')->required()->valid_email();
            $this->form_validation->name('phone')->required()->numeric();
            $this->form_validation->name('bio')->required()->min_length(10);
            
            // Run validation
            if ($this->form_validation->run()) {
                $data = [
                    'name' => filter_io('string', $this->io->post('name')),
                    'email' => filter_io('string', $this->io->post('email')),
                    'phone' => filter_io('string', $this->io->post('phone')),
                    'bio' => filter_io('string', $this->io->post('bio'))
                ];

                // Get the profile by user_id to get the actual profile id
                $profile = $this->ProfileModel->get_profile_by_user_id($user_id);
                if ($profile) {
                    $this->ProfileModel->update_record($profile['id'], $data);
                    $this->session->set_flashdata('success', 'Profile updated successfully');
                } else {
                    $this->session->set_flashdata('error', 'Profile not found');
                }
                redirect('profile');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                $data['profile'] = $this->ProfileModel->get_profile_with_user($user_id);
                $this->call->view('profile/edit', $data);
                return;
            }
        } else {
            $data['profile'] = $this->ProfileModel->get_profile_with_user($user_id);
            $this->call->view('profile/edit', $data);
        }
    }

    public function change_password() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('old_password')->required()->min_length(6);
            $this->form_validation->name('new_password')->required()->min_length(6);
            $this->form_validation->name('confirm_password')->required()->min_length(6);
            
            // Run validation
            if ($this->form_validation->run()) {
                $old = $this->io->post('old_password');
                $new = $this->io->post('new_password');
                $confirm = $this->io->post('confirm_password');

                if ($new === $confirm) {
                    $user_id = $this->session->userdata('user_id');
                    $this->UserModel->update_record($user_id, [
                        'password' => password_hash($new, PASSWORD_DEFAULT)
                    ]);
                    $this->session->set_flashdata('success', 'Password changed successfully');
                    redirect('profile');
                } else {
                    $this->session->set_flashdata('error', 'New password and confirm password do not match');
                }
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
            }
        }

        $this->call->view('profile/change_password');
    }
}