<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class WellnessFormController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('WellnessFormModel');
        $this->call->library('auth');
        $this->call->library('session');
        $this->call->library('pagination');
    }

    public function index($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        
        if ($role === 'counselor') {
            $total_forms = $this->WellnessFormModel->count_by_counselor($user_id);
            $base_url = 'wellness-forms/index';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_forms = $this->WellnessFormModel->get_forms_by_counselor($user_id);
        } else {
            $total_forms = $this->WellnessFormModel->count_all();
            $base_url = 'wellness-forms/index';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_forms = $this->WellnessFormModel->get_active_forms_with_submission_flag($user_id);
        }

        $page_data = $this->pagination->initialize(
            $total_forms,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['forms'] = array_slice($all_forms, $offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();

        $data['role'] = $role;
        $this->call->view('wellness_forms/index', $data);
    }

    public function create() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create wellness forms.');
            redirect('wellness-forms');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5)->max_length(255);
            $this->form_validation->name('description')->max_length(1000);
            
            // Validate that we have questions
            $questions = $this->io->post('questions');
            if (empty($questions) || !is_array($questions)) {
                $this->session->set_flashdata('error', 'At least one question is required.');
                redirect('wellness-forms/create');
            }
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = trim(filter_io('string', $this->io->post('title')));
                $description = trim(filter_io('string', $this->io->post('description')));
                $is_active = $this->io->post('is_active') ? 1 : 0;
                $questions = $this->io->post('questions');
                $question_types = $this->io->post('question_types');
                $scale_mins = $this->io->post('scale_min');
                $scale_maxs = $this->io->post('scale_max');

                if (empty($title)) {
                    $this->session->set_flashdata('error', 'Form title is required.');
                    redirect('wellness-forms/create');
                }
                            
                if (empty($questions) || !is_array($questions)) {
                    $this->session->set_flashdata('error', 'At least one question is required.');
                    redirect('wellness-forms/create');
                }

                $question_payload = [];
                foreach ($questions as $index => $question_text) {
                    $question_text = trim($question_text);
                    if (empty($question_text)) {
                        continue;
                    }

                    // Validate and sanitize question type
                    $type = isset($question_types[$index]) ? trim($question_types[$index]) : 'text';
                    $type = in_array($type, ['text', 'scale']) ? $type : 'text';
                    
                    $question_data = [
                        'question_text' => $question_text,
                        'question_type' => $type,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Process scale values for scale type questions
                    // Note: We're not including scale_min and scale_max in the database for now
                    // due to potential database schema issues
                    if ($type === 'scale') {
                        $scale_min = isset($scale_mins[$index]) && $scale_mins[$index] !== '' ? (int) $scale_mins[$index] : 1;
                        $scale_max = isset($scale_maxs[$index]) && $scale_maxs[$index] !== '' ? (int) $scale_maxs[$index] : 5;

                        // Validate scale values
                        if ($scale_min >= $scale_max) {
                            $scale_min = 1;
                            $scale_max = 5;
                        }
                        
                        // Ensure reasonable scale ranges
                        $scale_min = max(0, min(10, $scale_min));
                        $scale_max = max(1, min(20, $scale_max));
                        
                        // We're not adding scale values to the database for now
                        // This is a temporary workaround until the database schema is updated
                    }
                    // For text questions, we don't include scale_min and scale_max fields
                    
                    $question_payload[] = $question_data;
                }

                if (empty($question_payload)) {
                    $this->session->set_flashdata('error', 'Add at least one valid question with text.');
                    redirect('wellness-forms/create');
                }
                
                // Validate that we don't have too many questions (reasonable limit)
                if (count($question_payload) > 50) {
                    $this->session->set_flashdata('error', 'A form cannot have more than 50 questions.');
                    redirect('wellness-forms/create');
                }

                $form_data = [
                    'title' => $title,
                    'description' => $description,
                    'counselor_id' => $this->session->userdata('user_id'),
                    'is_active' => $is_active,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->WellnessFormModel->create_form_with_questions($form_data, $question_payload);
                $this->session->set_flashdata('success', 'Wellness form created successfully.');
                redirect('wellness-forms');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $error_message = 'Please correct the following errors:<br>' . implode('<br>', $errors);
                $this->session->set_flashdata('error', $error_message);
                redirect('wellness-forms/create');
            }
        }

        $this->call->view('wellness_forms/create');
    }

    public function view($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');

        $form = $this->WellnessFormModel->get_form_with_author($id);
        if (!$form) {
            $this->session->set_flashdata('error', 'Form not found.');
            redirect('wellness-forms');
        }

        $questions = $this->WellnessFormModel->get_questions($id);
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        $data = [
            'form' => $form,
            'questions' => $questions,
            'role' => $role,
            'has_submitted' => $role === 'student'
                ? $this->WellnessFormModel->has_student_submitted($id, $user_id)
                : false
        ];

        $this->call->view('wellness_forms/view', $data);
    }

    public function respond($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'student') {
            $this->session->set_flashdata('error', 'Only students can submit responses.');
            redirect('wellness-forms/view/' . $id);
        }

        $answers = $this->io->post('answers');
        $answers = is_array($answers) ? $answers : [];
        $questions = $this->WellnessFormModel->get_questions($id);

        if (empty($questions)) {
            $this->session->set_flashdata('error', 'Form has no questions configured.');
            redirect('wellness-forms');
        }

        if ($this->WellnessFormModel->has_student_submitted($id, $this->session->userdata('user_id'))) {
            $this->session->set_flashdata('error', 'You already submitted a response for this form.');
            redirect('wellness-forms/view/' . $id);
        }

        $prepared_answers = [];
        foreach ($questions as $question) {
            $question_id = $question['id'];
            $answer_text = isset($answers[$question_id]) ? trim($answers[$question_id]) : '';

            if ($question['question_type'] === 'scale' && $answer_text === '') {
                $this->session->set_flashdata('error', 'Please answer all scale questions.');
                redirect('wellness-forms/view/' . $id);
            }

            $prepared_answers[$question_id] = $answer_text;
        }

        $saved = $this->WellnessFormModel->save_response(
            $id,
            $this->session->userdata('user_id'),
            $prepared_answers
        );

        if ($saved) {
            $this->session->set_flashdata('success', 'Thank you for completing the wellness form.');
        } else {
            $this->session->set_flashdata('error', 'Unable to save your response.');
        }

        redirect('wellness-forms/view/' . $id);
    }

    public function responses($id, $page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can view responses.');
            redirect('wellness-forms');
        }

        $form = $this->WellnessFormModel->get_form_with_author($id);
        if (!$form || $form['counselor_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Form not found or unauthorized.');
            redirect('wellness-forms');
        }

        // Pagination
        $current_page = (int) segment(5) ?: 1;
        $rows_per_page = 6;
        $responses = $this->WellnessFormModel->get_responses_with_answers($id);
        $total_responses = count($responses);
        $base_url = 'wellness-forms/responses/' . $id;
        $offset = ($current_page - 1) * $rows_per_page;
        
        $page_data = $this->pagination->initialize(
            $total_responses,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $paginated_responses = array_slice($responses, $offset, $rows_per_page);
        
        $data = [
            'form' => $form,
            'responses' => $paginated_responses,
            'pagination' => $this->pagination->paginate()
        ];

        $this->call->view('wellness_forms/responses', $data);
    }

    public function toggle_status($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can update form status.');
            redirect('wellness-forms');
        }

        $form = $this->WellnessFormModel->get_form_with_author($id);
        if (!$form || $form['counselor_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Form not found or unauthorized.');
            redirect('wellness-forms');
        }

        $new_status = $form['is_active'] ? 0 : 1;
        $this->WellnessFormModel->update($id, ['is_active' => $new_status]);

        $this->session->set_flashdata('success', 'Form visibility updated.');
        redirect('wellness-forms');
    }
}

