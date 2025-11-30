<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class CareerGuidanceController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->model('CareerAssessmentModel');
        $this->call->model('CareerPathwayModel');
        $this->call->model('ScholarshipModel');
        $this->call->library('auth');
        $this->call->library('session');
        $this->call->library('pagination');
    }

    public function index($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        
        // Check if user has valid role
        if ($role !== 'student' && $role !== 'counselor') {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('auth/login');
        }
        
        // Get career pathways
        $data['pathways'] = $this->CareerPathwayModel->get_all();
        
        // Get active scholarships with pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        $total_scholarships = $this->ScholarshipModel->count_active();
        $base_url = 'career-guidance/index';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $page_data = $this->pagination->initialize(
            $total_scholarships,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['scholarships'] = $this->ScholarshipModel->get_active_paginated($offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();
        
        // Get assessments based on role
        if ($role === 'student') {
            $data['assessments'] = $this->CareerAssessmentModel->get_by_student($user_id);
        } else {
            // For counselors, show all assessments
            $data['assessments'] = $this->CareerAssessmentModel->get_all();
        }
        
        $data['role'] = $role;
        $this->call->view('career_guidance/index', $data);
    }

    public function pathways($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role !== 'student' && $role !== 'counselor') {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('auth/login');
        }
        
        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        $total_pathways = $this->CareerPathwayModel->count_all();
        $base_url = 'career-guidance/pathways';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $page_data = $this->pagination->initialize(
            $total_pathways,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['pathways'] = array_slice($this->CareerPathwayModel->get_all(), $offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();
        $data['role'] = $role;
        $this->call->view('career_guidance/pathways', $data);
    }

    public function pathway_details($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role !== 'student' && $role !== 'counselor') {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('auth/login');
        }
        
        $pathway = $this->CareerPathwayModel->get_by_id($id);
        if (!$pathway) {
            $this->session->set_flashdata('error', 'Career pathway not found');
            redirect('career-guidance/pathways');
        }
        
        $data['pathway'] = $pathway;
        $data['role'] = $role;
        $this->call->view('career_guidance/pathway_details', $data);
    }

    public function scholarships($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role !== 'student' && $role !== 'counselor') {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('auth/login');
        }
        
        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        $total_scholarships = $this->ScholarshipModel->count_all();
        $base_url = 'career-guidance/scholarships';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $page_data = $this->pagination->initialize(
            $total_scholarships,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['scholarships'] = array_slice($this->ScholarshipModel->get_all(), $offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();
        $data['role'] = $role;
        $this->call->view('career_guidance/scholarships', $data);
    }

    public function scholarship_details($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role !== 'student' && $role !== 'counselor') {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('auth/login');
        }
        
        $scholarship = $this->ScholarshipModel->get_by_id($id);
        if (!$scholarship) {
            $this->session->set_flashdata('error', 'Scholarship not found');
            redirect('career-guidance/scholarships');
        }
        
        $data['scholarship'] = $scholarship;
        $data['role'] = $role;
        $this->call->view('career_guidance/scholarship_details', $data);
    }

    public function assessments($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role !== 'student' && $role !== 'counselor') {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('auth/login');
        }
        
        $user_id = $this->session->userdata('user_id');
        
        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        
        if ($role === 'student') {
            $total_assessments = count($this->CareerAssessmentModel->get_by_student($user_id));
            $base_url = 'career-guidance/assessments';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_assessments = $this->CareerAssessmentModel->get_by_student($user_id);
        } else {
            $total_assessments = $this->CareerAssessmentModel->count_all();
            $base_url = 'career-guidance/assessments';
            $offset = ($current_page - 1) * $rows_per_page;
            $all_assessments = $this->CareerAssessmentModel->get_all();
        }

        $page_data = $this->pagination->initialize(
            $total_assessments,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $data['assessments'] = array_slice($all_assessments, $offset, $rows_per_page);
        $data['pagination'] = $this->pagination->paginate();
        
        $data['role'] = $role;
        $this->call->view('career_guidance/assessments', $data);
    }

    public function create_assessment() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create assessments');
            redirect('career-guidance/assessments');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('student_id')->required()->numeric();
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('type')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $student_id = (int) $this->io->post('student_id');
                $title = trim(filter_io('string', $this->io->post('title')));
                $type = filter_io('string', $this->io->post('type'));
                $score = $this->io->post('score') ? (float) $this->io->post('score') : null;
                $results = trim(filter_io('string', $this->io->post('results')));

                // Additional validation for allowed types
                $allowed_types = ['interest', 'aptitude', 'personality'];
                if (!in_array($type, $allowed_types)) {
                    $this->session->set_flashdata('error', 'Invalid assessment type');
                    redirect('career-guidance/create-assessment');
                }

                $data = [
                    'student_id' => $student_id,
                    'title' => $title,
                    'type' => $type,
                    'score' => $score,
                    'results' => $results,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->CareerAssessmentModel->create_record($data);
                $this->session->set_flashdata('success', 'Assessment created successfully');
                redirect('career-guidance/assessments');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/create-assessment');
            }
        } else {
            // Get all students for the dropdown
            $this->call->model('UserModel');
            $data['students'] = $this->UserModel->get_all_students();
            $this->call->view('career_guidance/create_assessment', $data);
        }
    }

    // Add new method for students to take assessments
    public function take_assessment($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'student') {
            $this->session->set_flashdata('error', 'Only students can take assessments');
            redirect('career-guidance/assessments');
        }

        $assessment = $this->CareerAssessmentModel->get_by_id($id);
        if (!$assessment) {
            $this->session->set_flashdata('error', 'Assessment not found');
            redirect('career-guidance/assessments');
        }

        // Check if this assessment belongs to the current student
        if ($assessment['student_id'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Unauthorized access to this assessment');
            redirect('career-guidance/assessments');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('results')->required()->min_length(10);
            
            // Run validation
            if ($this->form_validation->run()) {
                $score = $this->io->post('score') ? (float) $this->io->post('score') : null;
                $results = trim(filter_io('string', $this->io->post('results')));

                $data = [
                    'score' => $score,
                    'results' => $results,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->CareerAssessmentModel->update_record($id, $data);
                $this->session->set_flashdata('success', 'Assessment completed successfully');
                redirect('career-guidance/assessments');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/view/' . $id);
            }
        } else {
            $data['assessment'] = $assessment;
            $this->call->view('career_guidance/take_assessment', $data);
        }
    }

    // Add method for counselors to create career pathways
    public function create_pathway() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create career pathways');
            redirect('career-guidance/pathways');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('field')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = trim(filter_io('string', $this->io->post('title')));
                $description = trim(filter_io('string', $this->io->post('description')));
                $field = trim(filter_io('string', $this->io->post('field')));
                $education_required = trim(filter_io('string', $this->io->post('education_required')));
                $skills_required = trim(filter_io('string', $this->io->post('skills_required')));
                $job_outlook = trim(filter_io('string', $this->io->post('job_outlook')));
                $salary_range = trim(filter_io('string', $this->io->post('salary_range')));

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'field' => $field,
                    'education_required' => $education_required,
                    'skills_required' => $skills_required,
                    'job_outlook' => $job_outlook,
                    'salary_range' => $salary_range,
                    'created_by' => $this->session->userdata('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->CareerPathwayModel->create_record($data);
                $this->session->set_flashdata('success', 'Career pathway created successfully');
                redirect('career-guidance/pathways');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/create-pathway');
            }
        } else {
            $this->call->view('career_guidance/create_pathway');
        }
    }
    
    // Add method for counselors to edit career pathways
    public function edit_pathway($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can edit career pathways');
            redirect('career-guidance/pathways');
        }

        $pathway = $this->CareerPathwayModel->get_by_id($id);
        if (!$pathway) {
            $this->session->set_flashdata('error', 'Career pathway not found');
            redirect('career-guidance/pathways');
        }

        // Check if this pathway was created by the current counselor
        if ($pathway['created_by'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Unauthorized access to this career pathway');
            redirect('career-guidance/pathways');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('field')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = trim(filter_io('string', $this->io->post('title')));
                $description = trim(filter_io('string', $this->io->post('description')));
                $field = trim(filter_io('string', $this->io->post('field')));
                $education_required = trim(filter_io('string', $this->io->post('education_required')));
                $skills_required = trim(filter_io('string', $this->io->post('skills_required')));
                $job_outlook = trim(filter_io('string', $this->io->post('job_outlook')));
                $salary_range = trim(filter_io('string', $this->io->post('salary_range')));

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'field' => $field,
                    'education_required' => $education_required,
                    'skills_required' => $skills_required,
                    'job_outlook' => $job_outlook,
                    'salary_range' => $salary_range,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->CareerPathwayModel->update_record($id, $data);
                $this->session->set_flashdata('success', 'Career pathway updated successfully');
                redirect('career-guidance/pathways');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/edit-pathway/' . $id);
            }
        } else {
            $data['pathway'] = $pathway;
            $this->call->view('career_guidance/edit_pathway', $data);
        }
    }
    
    // Add method for counselors to delete career pathways
    public function delete_pathway($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can delete career pathways');
            redirect('career-guidance/pathways');
        }

        $pathway = $this->CareerPathwayModel->get_by_id($id);
        if (!$pathway) {
            $this->session->set_flashdata('error', 'Career pathway not found');
            redirect('career-guidance/pathways');
        }

        // Check if this pathway was created by the current counselor
        if ($pathway['created_by'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Unauthorized access to this career pathway');
            redirect('career-guidance/pathways');
        }

        $this->CareerPathwayModel->delete_record($id);
        $this->session->set_flashdata('success', 'Career pathway deleted successfully');
        redirect('career-guidance/pathways');
    }

    // Add method for counselors to create scholarships
    public function create_scholarship() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create scholarships');
            redirect('career-guidance/scholarships');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('provider')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = trim(filter_io('string', $this->io->post('title')));
                $description = trim(filter_io('string', $this->io->post('description')));
                $provider = trim(filter_io('string', $this->io->post('provider')));
                $eligibility_criteria = trim(filter_io('string', $this->io->post('eligibility_criteria')));
                $application_deadline = trim(filter_io('string', $this->io->post('application_deadline')));
                $award_amount = trim(filter_io('string', $this->io->post('award_amount')));
                $application_link = trim(filter_io('string', $this->io->post('application_link')));

                // Validate date if provided
                if (!empty($application_deadline)) {
                    $date = DateTime::createFromFormat('Y-m-d', $application_deadline);
                    if (!$date || $date->format('Y-m-d') !== $application_deadline) {
                        $this->session->set_flashdata('error', 'Invalid date format for application deadline');
                        redirect('career-guidance/create-scholarship');
                    }
                }

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'provider' => $provider,
                    'eligibility_criteria' => $eligibility_criteria,
                    'application_deadline' => $application_deadline ?: null,
                    'award_amount' => $award_amount,
                    'application_link' => $application_link,
                    'created_by' => $this->session->userdata('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->ScholarshipModel->create_record($data);
                $this->session->set_flashdata('success', 'Scholarship created successfully');
                redirect('career-guidance/scholarships');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/create-scholarship');
            }
        } else {
            $this->call->view('career_guidance/create_scholarship');
        }
    }
    
    // Add method for counselors to edit scholarships
    public function edit_scholarship($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can edit scholarships');
            redirect('career-guidance/scholarships');
        }

        $scholarship = $this->ScholarshipModel->get_by_id($id);
        if (!$scholarship) {
            $this->session->set_flashdata('error', 'Scholarship not found');
            redirect('career-guidance/scholarships');
        }

        // Check if this scholarship was created by the current counselor
        if ($scholarship['created_by'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Unauthorized access to this scholarship');
            redirect('career-guidance/scholarships');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('provider')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = trim(filter_io('string', $this->io->post('title')));
                $description = trim(filter_io('string', $this->io->post('description')));
                $provider = trim(filter_io('string', $this->io->post('provider')));
                $eligibility_criteria = trim(filter_io('string', $this->io->post('eligibility_criteria')));
                $application_deadline = trim(filter_io('string', $this->io->post('application_deadline')));
                $award_amount = trim(filter_io('string', $this->io->post('award_amount')));
                $application_link = trim(filter_io('string', $this->io->post('application_link')));

                // Validate date if provided
                if (!empty($application_deadline)) {
                    $date = DateTime::createFromFormat('Y-m-d', $application_deadline);
                    if (!$date || $date->format('Y-m-d') !== $application_deadline) {
                        $this->session->set_flashdata('error', 'Invalid date format for application deadline');
                        redirect('career-guidance/edit-scholarship/' . $id);
                    }
                }

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'provider' => $provider,
                    'eligibility_criteria' => $eligibility_criteria,
                    'application_deadline' => $application_deadline ?: null,
                    'award_amount' => $award_amount,
                    'application_link' => $application_link,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->ScholarshipModel->update_record($id, $data);
                $this->session->set_flashdata('success', 'Scholarship updated successfully');
                redirect('career-guidance/scholarships');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/edit-scholarship/' . $id);
            }
        } else {
            $data['scholarship'] = $scholarship;
            $this->call->view('career_guidance/edit_scholarship', $data);
        }
    }
    
    // Add method for counselors to delete scholarships
    public function delete_scholarship($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can delete scholarships');
            redirect('career-guidance/scholarships');
        }

        $scholarship = $this->ScholarshipModel->get_by_id($id);
        if (!$scholarship) {
            $this->session->set_flashdata('error', 'Scholarship not found');
            redirect('career-guidance/scholarships');
        }

        // Check if this scholarship was created by the current counselor
        if ($scholarship['created_by'] != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Unauthorized access to this scholarship');
            redirect('career-guidance/scholarships');
        }

        $this->ScholarshipModel->delete_record($id);
        $this->session->set_flashdata('success', 'Scholarship deleted successfully');
        redirect('career-guidance/scholarships');
    }
    
    // Add method for counselors to edit assessments
    public function edit_assessment($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can edit assessments');
            redirect('career-guidance/assessments');
        }

        $assessment = $this->CareerAssessmentModel->get_by_id($id);
        if (!$assessment) {
            $this->session->set_flashdata('error', 'Assessment not found');
            redirect('career-guidance/assessments');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('student_id')->required()->numeric();
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('type')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $student_id = (int) $this->io->post('student_id');
                $title = trim(filter_io('string', $this->io->post('title')));
                $type = filter_io('string', $this->io->post('type'));
                $score = $this->io->post('score') ? (float) $this->io->post('score') : null;
                $results = trim(filter_io('string', $this->io->post('results')));

                // Additional validation for allowed types
                $allowed_types = ['interest', 'aptitude', 'personality'];
                if (!in_array($type, $allowed_types)) {
                    $this->session->set_flashdata('error', 'Invalid assessment type');
                    redirect('career-guidance/edit-assessment/' . $id);
                }

                $data = [
                    'student_id' => $student_id,
                    'title' => $title,
                    'type' => $type,
                    'score' => $score,
                    'results' => $results,
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->CareerAssessmentModel->update_record($id, $data);
                $this->session->set_flashdata('success', 'Assessment updated successfully');
                redirect('career-guidance/assessments');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('career-guidance/edit-assessment/' . $id);
            }
        } else {
            // Get all students for the dropdown
            $this->call->model('UserModel');
            $data['students'] = $this->UserModel->get_all_students();
            $data['assessment'] = $assessment;
            $this->call->view('career_guidance/edit_assessment', $data);
        }
    }
    
    // Add method for counselors to delete assessments
    public function delete_assessment($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can delete assessments');
            redirect('career-guidance/assessments');
        }

        $assessment = $this->CareerAssessmentModel->get_by_id($id);
        if (!$assessment) {
            $this->session->set_flashdata('error', 'Assessment not found');
            redirect('career-guidance/assessments');
        }

        $this->CareerAssessmentModel->delete_record($id);
        $this->session->set_flashdata('success', 'Assessment deleted successfully');
        redirect('career-guidance/assessments');
    }

    // Add method for counselors to access the explore careers page (create pathway form)
    public function explore_careers() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create career pathways');
            redirect('career-guidance/pathways');
        }

        $this->call->view('career_guidance/explore_careers');
    }
    
    // Add method for counselors to access the find scholarships page (create scholarship form)
    public function find_scholarships() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') !== 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can create scholarships');
            redirect('career-guidance/scholarships');
        }

        $this->call->view('career_guidance/find_scholarships');
    }

}