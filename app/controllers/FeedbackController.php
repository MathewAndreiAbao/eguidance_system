<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class FeedbackController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('FeedbackModel');
        $this->call->model('UserModel');
        $this->call->library('auth');
        $this->call->library('session');
        $this->call->library('pagination');
    }

    public function index($page = 1) {
        if (!$this->auth->is_logged_in()) {
            redirect('auth/login');
        }

        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        
        if ($role == 'student') {
            $data['counselors'] = $this->UserModel->get_all_counselors();
            $total_feedback = count($this->FeedbackModel->get_for_student($user_id));
            $base_url = 'feedback/index';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_feedback = $this->FeedbackModel->get_for_student($user_id);
        } else {
            $total_feedback = count($this->FeedbackModel->get_for_counselor($user_id));
            $base_url = 'feedback/index';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_feedback = $this->FeedbackModel->get_for_counselor($user_id);
        }

        $page_data = $this->pagination->initialize(
            $total_feedback,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['feedback_list'] = array_slice($all_feedback, $offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();
        $data['role'] = $role;
        $this->call->view('feedback/index', $data);
    }

    public function create() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') != 'student') {
            $this->session->set_flashdata('error', 'Only students can submit feedback.');
            redirect('feedback');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('counselor_id')->required()->numeric();
            $this->form_validation->name('subject')->required()->min_length(5);
            $this->form_validation->name('message')->required()->min_length(10);
            $this->form_validation->name('rating')->numeric();
            
            // Run validation
            if ($this->form_validation->run()) {
                $counselor_id = (int) $this->io->post('counselor_id');
                $subject = trim(filter_io('string', $this->io->post('subject')));
                $content = trim(filter_io('string', $this->io->post('message')));
                $rating_raw = $this->io->post('rating');
                $rating = $rating_raw !== '' ? (int) $rating_raw : null;

                // Additional validation for rating range
                if (!is_null($rating) && ($rating < 1 || $rating > 5)) {
                    $this->session->set_flashdata('error', 'Rating must be between 1 and 5.');
                    redirect('feedback');
                }

                // Combine subject and message into the feedback field since the database doesn't have separate subject/content columns
                $feedback_content = "Subject: " . $subject . "\n\n" . $content;
                
                $this->FeedbackModel->create_feedback([
                    'student_id' => $this->session->userdata('user_id'),
                    'counselor_id' => $counselor_id,
                    'feedback' => $feedback_content,
                    'rating' => $rating,
                    'status' => 'new',
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $this->session->set_flashdata('success', 'Feedback submitted successfully.');
                redirect('feedback');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('feedback');
            }
        }
    }

    public function update_status($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') != 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can update feedback status.');
            redirect('feedback');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('status')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $status = filter_io('string', $this->io->post('status'));
                $allowed = ['new', 'in_review', 'resolved'];

                if (!in_array($status, $allowed)) {
                    $this->session->set_flashdata('error', 'Invalid status selected.');
                    redirect('feedback');
                }

                $updated = $this->FeedbackModel->update_status_for_counselor(
                    $id,
                    $this->session->userdata('user_id'),
                    $status
                );

                if ($updated !== false) {
                    $this->session->set_flashdata('success', 'Feedback status updated.');
                } else {
                    $this->session->set_flashdata('error', 'Unable to update feedback status.');
                }

                redirect('feedback');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('feedback');
            }
        }
    }
}

