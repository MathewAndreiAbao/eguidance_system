<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ResourceController extends Controller {
    

    public function __construct() {
        parent::__construct();
        $this->call->model('ResourceModel');
        $this->call->library('auth');
        $this->call->library('pagination');
    }

    public function index($page = 1) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        
        // Pagination
        $current_page = (int) segment(4) ?: 1;
        $rows_per_page = 6;
        $total_resources = $this->ResourceModel->count_all();
        $base_url = 'resources/index';
        $offset = ($current_page - 1) * $rows_per_page;
        
        $page_data = $this->pagination->initialize(
            $total_resources,
            $rows_per_page,
            $current_page,
            $base_url,
            5
        );
        
        $this->pagination->set_theme('tailwind');
        $limit_clause = $page_data['limit'];
        $paginated_resources = $this->ResourceModel->get_paginated($limit_clause);
        
        $data['resources'] = $paginated_resources;
        $data['pagination'] = $this->pagination->paginate();
        
        $this->call->view('resources/index', $data);
    }

    public function view($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $resource = $this->ResourceModel->get_by_id($id);
        if (!$resource) {
            $this->session->set_flashdata('error', 'Resource not found');
            redirect('resources');
        }
        $data['resource'] = $resource;
        $this->call->view('resources/view', $data);
    }

    public function download($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $resource = $this->ResourceModel->get_by_id($id);
        if (!$resource) {
            $this->session->set_flashdata('error', 'Resource not found');
            redirect('resources');
        }

        // Increment view count
        $this->ResourceModel->increment_views($id);

        // Handle different resource types
        if ($resource['type'] === 'link') {
            // For links, redirect to the external URL
            redirect($resource['file_path']);

        } elseif ($resource['type'] === 'video' || $resource['type'] === 'audio') {
            // For media files, serve them directly
            $file = './public/uploads/resources/' . $resource['file_path'];
            if (!file_exists($file)) {
                $this->session->set_flashdata('error', 'File not found');
                redirect('resources');
            }

            // Set appropriate content type
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $mimeTypes = [
                'mp4' => 'video/mp4',
                'avi' => 'video/x-msvideo',
                'mov' => 'video/quicktime',
                'wmv' => 'video/x-ms-wmv',
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg'
            ];
            
            $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';

            header('Content-Description: File Transfer');
            header('Content-Type: ' . $contentType);
            header('Content-Disposition: inline; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            // For documents, force download
            $file = './public/uploads/resources/' . $resource['file_path'];
            if (!file_exists($file)) {
                $this->session->set_flashdata('error', 'File not found');
                redirect('resources');
            }

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }

    public function create() {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        if ($this->session->userdata('role') != 'counselor') {
            $this->session->set_flashdata('error', 'Only counselors can upload resources');
            redirect('resources');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('description')->required()->min_length(10);
            $this->form_validation->name('resource_type')->required();
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = filter_io('string', $this->io->post('title'));
                $description = filter_io('string', $this->io->post('description'));
                $resourceType = filter_io('string', $this->io->post('resource_type'));

                $data = [
                    'title' => $title,
                    'description' => $description,
                    'counselor_id' => $this->session->userdata('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

            if ($resourceType === 'link') {
                // Handle external link
                $link = filter_io('string', $this->io->post('resource_link'));
                if (empty($link)) {
                    $this->session->set_flashdata('error', 'Please provide a link');
                    redirect('resources/create');
                }
                
                $data['file_path'] = $link;
                $data['type'] = 'link';
            } else {
                // Handle file upload
                if (!isset($_FILES['resource_file']) || $_FILES['resource_file']['error'] != UPLOAD_ERR_OK) {
                    $this->session->set_flashdata('error', 'Please provide a file to upload');
                    redirect('resources/create');
                }

                $uploadDir = './public/uploads/resources';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0755, true);
                }

                $originalName = basename($_FILES['resource_file']['name']);
                $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                $filename = uniqid('res_') . '.' . $ext;
                $targetPath = $uploadDir . '/' . $filename;

                if (!move_uploaded_file($_FILES['resource_file']['tmp_name'], $targetPath)) {
                    $this->session->set_flashdata('error', 'Failed to move uploaded file');
                    redirect('resources/create');
                }

                $data['file_path'] = $filename;
                
                // Determine resource type based on file extension
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $type = 'document';
                if (in_array($extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                    $type = 'video';
                } elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) {
                    $type = 'audio';
                }
                $data['type'] = $type;
            }

            $this->ResourceModel->create_record($data);
            $this->session->set_flashdata('success', 'Resource uploaded successfully');
            redirect('resources');
        } else {
            // Validation failed, show errors
            $errors = $this->form_validation->get_errors();
            $this->session->set_flashdata('error', implode('<br>', $errors));
            redirect('resources/create');
        }
        } else {
            $this->call->view('resources/create');
        }
    }

    public function edit($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $resource = $this->ResourceModel->get_by_id($id);
        if (!$resource) {
            $this->session->set_flashdata('error', 'Resource not found');
            redirect('resources');
        }

        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        if ($role != 'counselor' || $resource['counselor_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('resources');
        }

        if ($_POST) {
            // Load form validation library
            $this->call->library('form_validation');
            
            // Set validation rules
            $this->form_validation->name('title')->required()->min_length(5);
            $this->form_validation->name('description')->required()->min_length(10);
            
            // Run validation
            if ($this->form_validation->run()) {
                $title = filter_io('string', $this->io->post('title'));
                $description = filter_io('string', $this->io->post('description'));
                $update = [
                    'title' => $title,
                    'description' => $description,
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Handle YouTube video resources
                // Handle other link resources
                if ($resource['type'] === 'link') {
                    $link = filter_io('string', $this->io->post('resource_link'));
                    if (!empty($link)) {
                        $update['file_path'] = $link;
                    }
                } else {
                    // Update type if file is replaced
                    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == UPLOAD_ERR_OK) {
                        $originalName = basename($_FILES['resource_file']['name']);
                        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        $type = 'document';
                        if (in_array($extension, ['mp4', 'avi', 'mov', 'wmv'])) {
                            $type = 'video';
                        } elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) {
                            $type = 'audio';
                        }
                        $update['type'] = $type;
                    }

                    // Optionally replace file
                    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == UPLOAD_ERR_OK) {
                        $uploadDir = './public/uploads/resources';
                        if (!is_dir($uploadDir)) {
                            @mkdir($uploadDir, 0755, true);
                        }

                        $originalName = basename($_FILES['resource_file']['name']);
                        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                        $filename = uniqid('res_') . '.' . $ext;
                        $targetPath = $uploadDir . '/' . $filename;

                        if (move_uploaded_file($_FILES['resource_file']['tmp_name'], $targetPath)) {
                            // remove old file
                            $old = $uploadDir . '/' . $resource['file_path'];
                            if (file_exists($old)) @unlink($old);
                            $update['file_path'] = $filename;
                        }
                    }
                }

                $this->ResourceModel->update_record($id, $update);
                $this->session->set_flashdata('success', 'Resource updated successfully');
                redirect('resources');
            } else {
                // Validation failed, show errors
                $errors = $this->form_validation->get_errors();
                $this->session->set_flashdata('error', implode('<br>', $errors));
                $data['resource'] = $resource;
                $this->call->view('resources/edit', $data);
                return;
            }
        } else {
            $data['resource'] = $resource;
            $this->call->view('resources/edit', $data);
        }
    }

    public function delete($id) {
        if (!$this->auth->is_logged_in()) redirect('auth/login');
        $resource = $this->ResourceModel->get_by_id($id);
        if (!$resource) {
            $this->session->set_flashdata('error', 'Resource not found');
            redirect('resources');
        }

        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        if ($role != 'counselor' || $resource['counselor_id'] != $user_id) {
            $this->session->set_flashdata('error', 'Unauthorized access');
            redirect('resources');
        }

        $uploadDir = './public/uploads/resources';
        $file = $uploadDir . '/' . $resource['file_path'];
        if (file_exists($file)) @unlink($file);

        $this->ResourceModel->delete_record($id);
        $this->session->set_flashdata('success', 'Resource deleted successfully');
        redirect('resources');
    }
}
