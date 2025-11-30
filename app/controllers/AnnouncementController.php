<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AnnouncementController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('AnnouncementModel');
        $this->call->model('UserModel');
        $this->call->library('auth');
        $this->call->library('pagination');

        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
        }
    }

    public function index($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        
        if ($role == 'counselor') {
            $total_announcements = $this->AnnouncementModel->count_all();
            $base_url = 'announcements/index';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_announcements = $this->AnnouncementModel->get_all();
            $data['can_create'] = true;
        } else {
            // Students can only view announcements
            $total_announcements = $this->AnnouncementModel->count_all();
            $base_url = 'announcements/index';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_announcements = $this->AnnouncementModel->get_all();
            $data['can_create'] = false;
        }

        $page_data = $this->pagination->initialize(
            $total_announcements,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['announcements'] = array_slice($all_announcements, $offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();

        $this->call->view('announcements/index', $data);
    }

    public function create() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        if ($this->session->userdata('role') != 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create announcements');
            redirect('announcements');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('content')->required()->min_length(10);
            
            // Run validation
            if ($this->form_validation->run()) {
                $data = [
                    'title' => filter_io('string', $this->io->post('title')),
                    'content' => filter_io('string', $this->io->post('content')),
                    'counselor_id' => $this->session->userdata('user_id'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->AnnouncementModel->create_record($data);
                $this->session->set_flashdata('success', 'Announcement created successfully');
                redirect('announcements');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('announcements/create');
            }
        }

        $this->call->view('announcements/create');
    }

    public function edit($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        if ($this->session->userdata('role') != 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can edit announcements');
            redirect('announcements');
        }

        $announcement = $this->AnnouncementModel->get_by_id($id);

        if (!$announcement) {
            $this->session->set_flashdata('error', 'Announcement not found');
            redirect('announcements');
        }

        // Check if counselor owns this announcement
        if ($announcement['counselor_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You can only edit your own announcements');
            redirect('announcements');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('content')->required()->min_length(10);
            
            // Run validation
            if ($this->form_validation->run()) {
                $data = [
                    'title' => filter_io('string', $this->io->post('title')),
                    'content' => filter_io('string', $this->io->post('content')),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->AnnouncementModel->update_record($id, $data);
                $this->session->set_flashdata('success', 'Announcement updated successfully');
                redirect('announcements');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                $data['announcement'] = $announcement;
                $this->call->view('announcements/edit', $data);
                return;
            }
        } else {
            $data['announcement'] = $announcement;
            $this->call->view('announcements/edit', $data);
        }
    }

    public function delete($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        if ($this->session->userdata('role') != 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can delete announcements');
            redirect('announcements');
        }

        $announcement = $this->AnnouncementModel->get_by_id($id);

        if (!$announcement) {
            $this->session->set_flashdata('error', 'Announcement not found');
            redirect('announcements');
        }

        // Check if counselor owns this announcement
        if ($announcement['counselor_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You can only delete your own announcements');
            redirect('announcements');
        }

        $this->AnnouncementModel->delete_record($id);
        $this->session->set_flashdata('success', 'Announcement deleted successfully');
        redirect('announcements');
    }

    public function view($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        $announcement = $this->AnnouncementModel->get_by_id($id);

        if (!$announcement) {
            $this->session->set_flashdata('error', 'Announcement not found');
            redirect('announcements');
        }

        $data['announcement'] = $announcement;
        $this->call->view('announcements/view', $data);
    }
}
