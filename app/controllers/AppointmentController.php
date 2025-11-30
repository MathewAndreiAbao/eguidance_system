<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AppointmentController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->call->database();
        $this->call->model('AppointmentModel');
        $this->call->model('UserModel');
        $this->call->library('auth');
        $this->call->library('pagination');
        $this->call->library('APIIntegration');
    }

    /**
     * Main appointment dashboard - redirects to role-specific dashboard
     */
    public function index() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        
        switch ($role) {
            case 'student':
                redirect('appointments/student_dashboard');
                break;
            case 'counselor':
                redirect('appointments/counselor_dashboard');
                break;
            case 'admin':
                redirect('appointments/admin_dashboard');
                break;
            default:
                $this->session->set_flashdata('error', 'Invalid user role.');
                redirect('auth/login');
        }
    }

    /**
     * Student-specific appointment dashboard
     * Shows only appointments belonging to the logged-in student
     */
    public function student_dashboard($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role != 'student') {
            $this->session->set_flashdata('error', 'Access denied. Student access only.');
            redirect('appointments');
        }
        
        $user_id = $this->session->userdata('user_id');
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date';
        $sort_dir = (isset($_GET['sort_dir']) && strtolower($_GET['sort_dir']) === 'desc') ? 'desc' : 'asc';
        
        // Pagination
        $current_page = (int) segment(3) ?: 1;
        $rows_per_page = 6;
        $all_appointments = $this->AppointmentModel->get_student_appointments($user_id);
        $total_appointments = count($all_appointments);
        $base_url = 'appointments/student_dashboard';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $paginated_appointments = array_slice($all_appointments, $offset, $rows_per_page);
        
        $page_data = $this->pagination->initialize(
            $total_appointments,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        
        $data['appointments'] = $this->filter_and_sort_appointments($paginated_appointments, $search, $sort_by, $sort_dir);
        $data['filters'] = [
            'search' => $search,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir
        ];
        $data['pagination'] = $this->pagination->paginate();
        
        $this->call->view('appointments/student_dashboard', $data);
    }

    /**
     * Counselor-specific appointment dashboard
     * Shows all appointments assigned to the logged-in counselor
     */
    public function counselor_dashboard($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role != 'counselor') {
            $this->session->set_flashdata('error', 'Access denied. Counselor access only.');
            redirect('appointments');
        }
        
        $user_id = $this->session->userdata('user_id');
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date';
        $sort_dir = (isset($_GET['sort_dir']) && strtolower($_GET['sort_dir']) === 'desc') ? 'desc' : 'asc';
        
        // Pagination
        $current_page = (int) segment(3) ?: 1;
        $rows_per_page = 6;
        $all_appointments = $this->AppointmentModel->get_counselor_appointments($user_id);
        $total_appointments = count($all_appointments);
        $base_url = 'appointments/counselor_dashboard';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $paginated_appointments = array_slice($all_appointments, $offset, $rows_per_page);
        
        $page_data = $this->pagination->initialize(
            $total_appointments,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        
        $data['appointments'] = $this->filter_and_sort_appointments($paginated_appointments, $search, $sort_by, $sort_dir);
        $data['filters'] = [
            'search' => $search,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir
        ];
        $data['pagination'] = $this->pagination->paginate();
        
        $this->call->view('appointments/counselor_dashboard', $data);
    }

    /**
     * Admin appointment management
     * Shows all appointments in the system
     */
    public function admin_dashboard($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role != 'admin') {
            $this->session->set_flashdata('error', 'Access denied. Admin access only.');
            redirect('appointments');
        }
        
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date';
        $sort_dir = (isset($_GET['sort_dir']) && strtolower($_GET['sort_dir']) === 'desc') ? 'desc' : 'asc';
        
        // Pagination
        $current_page = (int) segment(3) ?: 1;
        $rows_per_page = 6;
        $all_appointments = $this->AppointmentModel->get_all();
        $total_appointments = count($all_appointments);
        $base_url = 'appointments/admin_dashboard';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $paginated_appointments = array_slice($all_appointments, $offset, $rows_per_page);
        
        $page_data = $this->pagination->initialize(
            $total_appointments,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        
        $data['appointments'] = $this->filter_and_sort_appointments($paginated_appointments, $search, $sort_by, $sort_dir);
        $data['filters'] = [
            'search' => $search,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir
        ];
        $data['pagination'] = $this->pagination->paginate();
        
        $this->call->view('appointments/admin_dashboard', $data);
    }

    public function book() {
        // Alias for create method
        $this->create();
    }

    public function create() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') != 'student') {
            $this->session->set_flashdata('error', 'Only students can create appointments');
            redirect('appointments');
        }

        // Always fetch counselors for the view
        $data['counselors'] = $this->UserModel->get_all_counselors();
        
        // Get holidays using Calendarific API and format for frontend
        $raw_holidays = $this->APIIntegration->get_holidays();
        $holiday_dates = [];
        foreach ($raw_holidays as $holiday) {
            if (isset($holiday['date']['iso'])) {
                $holiday_dates[] = $holiday['date']['iso'];
            }
        }
        $data['holidays'] = $holiday_dates;

        // Load form validation library
        $this->call->library('form_validation');

        if ($_POST) {
            
            // Set validation rules
            $this->form_validation->name('date')->required();
            $this->form_validation->name('time')->required();
            $this->form_validation->name('purpose')->required()->min_length(10);
            
            // Run validation
            if ($this->form_validation->run()) {
                $appointment_data = [
                    'student_id' => $this->session->userdata('user_id'),
                    'date' => filter_io('string', $this->io->post('date')),
                    'time' => filter_io('string', $this->io->post('time')),
                    'purpose' => filter_io('string', $this->io->post('purpose')),
                    'counselor_id' => filter_io('string', $this->io->post('counselor_id')),
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ];
            
            // Check if the selected date is a holiday using Calendarific API
            $is_holiday = $this->APIIntegration->is_holiday($appointment_data['date']);
            if ($is_holiday) {
                $this->session->set_flashdata('error', 'The selected date is a holiday: ' . $is_holiday);
                $this->call->view('appointments/create', $data);
                return;
            }

            if ($this->AppointmentModel->is_time_slot_available($appointment_data['date'], $appointment_data['time'], $appointment_data['counselor_id'])) {
                $appointment_id = $this->AppointmentModel->create_record($appointment_data);
                
                // SMS notification temporarily disabled
                // $this->APIIntegration->send_appointment_reminder($appointment_data);
                
                $this->session->set_flashdata('success', 'Appointment created successfully! (SMS notification temporarily disabled)');
                redirect('appointments');
            } else {
                $this->session->set_flashdata('error', 'Time slot is not available');
                $this->call->view('appointments/create', $data);
                return;
            }
        } else {
            // Validation failed, show errors
            $errors = $this->form_validation->get_errors();
            // Handle both array and string return types from get_errors()
            if (is_array($errors)) {
                $error_message = implode('<br>', $errors);
            } else {
                $error_message = (string) $errors;
            }
            $this->session->set_flashdata('error', $error_message);
            $this->call->view('appointments/create', $data);
            return;
        }
        }
        
        $this->call->view('appointments/create', $data);
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

        // Role-based access control
        if ($role == 'student' && $appointment['student_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('appointments/student_dashboard');
        }
        
        if ($role == 'counselor' && $appointment['counselor_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('appointments/counselor_dashboard');
        }
        
        if ($role == 'admin') {
            // Admins can edit any appointment
        } else if ($role != 'student' && $role != 'counselor') {
            $this->session->set_flashdata('error', 'Invalid role');
            redirect('appointments');
        }

        // Load form validation library
        $this->call->library('form_validation');

        if ($_POST) {
            
            if ($role == 'student') {
                // Set validation rules for student
                $this->form_validation->name('date')->required();
                $this->form_validation->name('time')->required();
                $this->form_validation->name('purpose')->required()->min_length(5);
                
                $posted_counselor_id = filter_io('string', $this->io->post('counselor_id'));
                $data = [
                    'date' => filter_io('string', $this->io->post('date')),
                    'time' => filter_io('string', $this->io->post('time')),
                    'purpose' => filter_io('string', $this->io->post('purpose')),
                    'counselor_id' => !empty($posted_counselor_id) ? $posted_counselor_id : ($appointment['counselor_id'] ?? null)
                ];
            } elseif ($role == 'counselor') {
                // Set validation rules for counselor
                $this->form_validation->name('status')->required();
                
                $data = [
                    'status' => filter_io('string', $this->io->post('status'))
                ];
            }

            // Run validation
            if ($this->form_validation->run()) {
                // If student is updating date/time, ensure the new slot is still available
                if ($role == 'student') {
                    $counselor_id = $data['counselor_id'] ?? ($appointment['counselor_id'] ?? null);
                    if (!$this->AppointmentModel->is_time_slot_available($data['date'], $data['time'], $counselor_id, $id)) {
                        $this->session->set_flashdata('error', 'Time slot is not available');
                        redirect('appointments/edit/'.$id);
                    }
                }

                $this->AppointmentModel->update_record($id, $data);
            
            // SMS notification temporarily disabled
            // $updated_appointment = $this->AppointmentModel->get_by_id($id);
            // if ($updated_appointment) {
            //     $this->APIIntegration->send_appointment_reminder($updated_appointment);
            // }
            
            $this->session->set_flashdata('success', 'Appointment updated successfully! (SMS notification temporarily disabled)');

            // Redirect to appropriate dashboard based on role
            switch ($role) {
                case 'student':
                    redirect('appointments/student_dashboard');
                    break;
                case 'counselor':
                    redirect('appointments/counselor_dashboard');
                    break;
                case 'admin':
                    redirect('appointments/admin_dashboard');
                    break;
                default:
                    redirect('appointments');
            }
        }
        } else {
            // Validation failed, show errors
            $errors = $this->form_validation->get_errors();
            // Handle both array and string return types from get_errors()
            if (is_array($errors)) {
                $error_message = implode('<br>', $errors);
            } else {
                $error_message = (string) $errors;
            }
            $this->session->set_flashdata('error', $error_message);
            $data['appointment'] = $appointment;
            $data['counselors'] = $this->UserModel->get_all_counselors();
            $this->call->view('appointments/edit', $data);
            return;
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

        // Role-based access control
        if ($role == 'student' && $appointment['student_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('appointments/student_dashboard');
        }
        
        if ($role == 'counselor' && $appointment['counselor_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('appointments/counselor_dashboard');
        }
        
        if ($role == 'admin') {
            // Admins can delete any appointment
        } else if ($role != 'student' && $role != 'counselor') {
            $this->session->set_flashdata('error', 'Invalid role');
            redirect('appointments');
        }

        $this->AppointmentModel->delete_record($id);
        $this->session->set_flashdata('success', 'Appointment deleted successfully');
        
        // Redirect to appropriate dashboard based on role
        switch ($role) {
            case 'student':
                redirect('appointments/student_dashboard');
                break;
            case 'counselor':
                redirect('appointments/counselor_dashboard');
                break;
            case 'admin':
                redirect('appointments/admin_dashboard');
                break;
            default:
                redirect('appointments');
        }
    }

    public function check_availability() {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        if (!$this->auth->is_logged_in()) {
            echo json_encode(['error' => 'Not authenticated']);
            return;
        }

        $date = $this->io->post('date');
        $counselor_id = $this->io->post('counselor_id');

        if (empty($date)) {
            echo json_encode(['error' => 'Date is required']);
            return;
        }

        // Get booked time slots for the selected date and counselor
        $booked_slots = [];
        
        try {
            $conditions = ['date' => $date];

            if (!empty($counselor_id)) {
                $conditions['counselor_id'] = $counselor_id;
            }

            $filtered = $this->AppointmentModel->filter($conditions);
            $appointments = $filtered ? $filtered->get_all() : [];

            if (is_array($appointments)) {
                foreach ($appointments as $appointment) {
                    if (($appointment['status'] ?? 'pending') !== 'cancelled') {
                        $booked_slots[] = $appointment['time'];
                    }
                }
            }

            $booked_slots = array_values(array_unique($booked_slots));
            echo json_encode(['booked_slots' => $booked_slots]);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to check availability', 'booked_slots' => []]);
        }
    }

    public function upcoming($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        
        // Pagination
        $current_page = (int) segment(3) ?: 1;
        $rows_per_page = 6;
        $total_appointments = count($this->AppointmentModel->get_upcoming_appointments($user_id, $role));
        $base_url = 'appointments/upcoming';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $all_appointments = $this->AppointmentModel->get_upcoming_appointments($user_id, $role);
        $paginated_appointments = array_slice($all_appointments, $offset, $rows_per_page);
        
        $page_data = $this->pagination->initialize(
            $total_appointments,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        
        $data['appointments'] = $paginated_appointments;
        $data['pagination'] = $this->pagination->paginate();
        
        $this->call->view('appointments/upcoming', $data);
    }

    /**
     * Counselor-specific view for managing all appointments
     * Shows all appointments for counselors to manage
     */
    public function manage() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        $role = $this->session->userdata('role');
        if ($role != 'counselor') {
            $this->session->set_flashdata('error', 'Access denied. Counselor access only.');
            redirect('appointments');
        }
        
        $user_id = $this->session->userdata('user_id');
        
        // Get all appointments for this counselor
        $appointments = $this->AppointmentModel->get_counselor_appointments($user_id);
        
        // Apply search and sorting if needed
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : '';
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date';
        $sort_dir = (isset($_GET['sort_dir']) && strtolower($_GET['sort_dir']) === 'desc') ? 'desc' : 'asc';
        
        $data['appointments'] = $this->filter_and_sort_appointments($appointments, $search, $sort_by, $sort_dir);
        $data['filters'] = [
            'search' => $search,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir
        ];
        
        $this->call->view('appointments/manage', $data);
    }

    /**
     * Apply search filtering and sorting to appointment lists.
     */
    private function filter_and_sort_appointments(array $appointments, ?string $search, string $sort_by, string $sort_dir): array
    {
        $filtered = $appointments;

        if (!empty($search)) {
            $needle = function_exists('mb_strtolower') ? mb_strtolower($search) : strtolower($search);
            $filtered = array_values(array_filter($appointments, function ($appointment) use ($needle) {
                $fields = [
                    $appointment['purpose'] ?? '',
                    $appointment['student_name'] ?? '',
                    $appointment['counselor_name'] ?? '',
                    $appointment['status'] ?? '',
                    $appointment['date'] ?? '',
                    $appointment['time'] ?? ''
                ];

                foreach ($fields as $value) {
                    $haystack = function_exists('mb_strtolower') ? mb_strtolower((string) $value) : strtolower((string) $value);
                    if ($value !== null && strpos($haystack, $needle) !== false) {
                        return true;
                    }
                }
                return false;
            }));
        }

        $sortable_fields = [
            'date' => 'date',
            'time' => 'time',
            'status' => 'status',
            'counselor' => 'counselor_name',
            'student' => 'student_name',
            'created' => 'created_at'
        ];

        $sort_key = $sortable_fields[$sort_by] ?? 'date';
        $direction = $sort_dir === 'desc' ? -1 : 1;

        usort($filtered, function ($a, $b) use ($sort_key, $direction) {
            $lower = function ($value) {
                $string = (string) ($value ?? '');
                return function_exists('mb_strtolower') ? mb_strtolower($string) : strtolower($string);
            };

            $valA = $lower($a[$sort_key] ?? '');
            $valB = $lower($b[$sort_key] ?? '');

            if ($valA == $valB) {
                return 0;
            }

            return ($valA < $valB ? -1 : 1) * $direction;
        });

        return $filtered;
    }
    
    public function holidays() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        // Get holidays using Calendarific API
        $raw_holidays = $this->APIIntegration->get_holidays();
        
        // Sort holidays by date
        usort($raw_holidays, function($a, $b) {
            return strtotime($a['date']['iso']) - strtotime($b['date']['iso']);
        });
        
        $data['holidays'] = $raw_holidays;
        $this->call->view('appointments/holidays', $data);
    }
    
    /**
     * Update appointment status via AJAX
     * Only counselors can update appointment status
     */
    public function update_status($id) {
        if (!$this->auth->is_logged_in()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            return;
        }
        
        if ($this->session->userdata('role') != 'counselor') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Only counselors can update appointment status']);
            return;
        }
        
        if ($_POST) {
            $status = filter_io('string', $this->io->post('status'));
            $allowed_statuses = ['pending', 'approved', 'completed', 'cancelled'];
            
            if (!in_array($status, $allowed_statuses)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                return;
            }
            
            $appointment = $this->AppointmentModel->get_by_id($id);
            
            if (!$appointment) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Appointment not found']);
                return;
            }
            
            // Check if the counselor owns this appointment
            if ($appointment['counselor_id'] != $this->session->userdata('user_id')) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
                return;
            }
            
            $updated = $this->AppointmentModel->update_status_for_counselor(
                $id,
                $this->session->userdata('user_id'),
                $status
            );
            
            if ($updated !== false) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Appointment status updated', 'status' => $status]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update appointment status']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }
}
