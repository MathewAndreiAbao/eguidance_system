<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProfileController extends Controller {
    public function __construct() {
        parent::__construct();
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
            $data = [
                'name' => filter_io('string', $this->io->post('name')),
                'email' => filter_io('string', $this->io->post('email')),
                'phone' => filter_io('string', $this->io->post('phone')),
                'bio' => filter_io('string', $this->io->post('bio'))
            ];

            // Handle picture upload if present
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
                $config['upload_path'] = './public/uploads/profiles/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = true;

                $this->call->library('upload', $config);

                if ($this->upload->do_upload('picture')) {
                    $upload_data = $this->upload->data();
                    $data['picture'] = 'uploads/profiles/' . $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors());
                    redirect('profile/edit');
                }
            }

            $this->ProfileModel->update_record($user_id, $data);
            $this->session->set_flashdata('success', 'Profile updated successfully');
            redirect('profile');
        } else {
            $data['profile'] = $this->ProfileModel->get_profile_with_user($user_id);
            $this->call->view('profile/edit', $data);
        }
    }

    public function change_password() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        if ($_POST) {
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
            }
        }

        $this->call->view('profile/change_password');
    }
}
