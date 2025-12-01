<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
*/

$router = new Router();

// Authentication
$router->get('/', 'AuthController@login');
$router->match('/auth/login', 'AuthController@login', 'GET|POST');
$router->match('/auth/register', 'AuthController@register', 'GET|POST');
$router->match('/auth/process_register', function() {
    redirect('auth/register');
}, 'GET|POST');
$router->get('/auth/logout', 'AuthController@logout');


// Student Dashboard
$router->get('/student/dashboard', 'DashboardController@student');

// Counselor Dashboard
$router->get('/counselor/dashboard', 'DashboardController@counselor');

// Profile Management
$router->get('/profile', 'ProfileController@index');
$router->match('/profile/edit', 'ProfileController@edit', 'GET|POST');
$router->match('/profile/update', 'ProfileController@update', 'GET|POST');

// Appointments
$router->get('/appointments', 'AppointmentController@index');
$router->get('/appointments/student_dashboard', 'AppointmentController@student_dashboard');
$router->get('/appointments/student_dashboard/{page}', 'AppointmentController@student_dashboard');
$router->get('/appointments/counselor_dashboard', 'AppointmentController@counselor_dashboard');
$router->get('/appointments/counselor_dashboard/{page}', 'AppointmentController@counselor_dashboard');
$router->get('/appointments/admin_dashboard', 'AppointmentController@admin_dashboard');
$router->get('/appointments/admin_dashboard/{page}', 'AppointmentController@admin_dashboard');
$router->get('/appointments/book', 'AppointmentController@book');
$router->match('/appointments/create', 'AppointmentController@create', 'GET|POST');
$router->get('/appointments/view/{id}', 'AppointmentController@view');
$router->match('/appointments/edit/{id}', 'AppointmentController@edit', 'GET|POST');
$router->get('/appointments/delete/{id}', 'AppointmentController@delete');
$router->get('/appointments/check_availability', 'AppointmentController@check_availability');
$router->post('/appointments/check_availability', 'AppointmentController@check_availability');
$router->post('/appointments/update_status/{id}', 'AppointmentController@update_status');
$router->get('/appointments/holidays', 'AppointmentController@holidays');

// Resources
$router->get('/resources', 'ResourceController@index');
$router->get('/resources/index/{page}', 'ResourceController@index');
$router->get('/resources/view/{id}', 'ResourceController@view');
$router->get('/resources/download/{id}', 'ResourceController@download');
$router->match('/resources/create', 'ResourceController@create', 'GET|POST');
$router->match('/resources/edit/{id}', 'ResourceController@edit', 'GET|POST');
$router->get('/resources/delete/{id}', 'ResourceController@delete');

// Announcements
$router->get('/announcements', 'AnnouncementController@index');
$router->get('/announcements/index/{page}', 'AnnouncementController@index');
$router->get('/announcements/view/{id}', 'AnnouncementController@view');
$router->match('/announcements/create', 'AnnouncementController@create', 'GET|POST');
$router->match('/announcements/edit/{id}', 'AnnouncementController@edit', 'GET|POST');
$router->get('/announcements/delete/{id}', 'AnnouncementController@delete');

// Feedback
$router->get('/feedback', 'FeedbackController@index');
$router->get('/feedback/index/{page}', 'FeedbackController@index');
$router->match('/feedback/create', 'FeedbackController@create', 'GET|POST');
$router->match('/feedback/edit/{id}', 'FeedbackController@edit', 'GET|POST');
$router->match('/feedback/update_status/{id}', 'FeedbackController@update_status', 'POST');
$router->get('/feedback/delete/{id}', 'FeedbackController@delete');

// Wellness Forms
$router->get('/wellness-forms', 'WellnessFormController@index');
$router->get('/wellness-forms/index/{page}', 'WellnessFormController@index');
$router->get('/wellness-forms/view/{id}', 'WellnessFormController@view');
$router->match('/wellness-forms/create', 'WellnessFormController@create', 'GET|POST');
$router->match('/wellness-forms/edit/{id}', 'WellnessFormController@edit', 'GET|POST');
$router->get('/wellness-forms/delete/{id}', 'WellnessFormController@delete');
$router->get('/wellness-forms/responses/{id}', 'WellnessFormController@responses');
$router->get('/wellness-forms/responses/{id}/{page}', 'WellnessFormController@responses');
$router->get('/wellness-forms/toggle_status/{id}', 'WellnessFormController@toggle_status');
$router->post('/wellness-forms/respond/{id}', 'WellnessFormController@respond');

// Career Guidance
$router->get('/career-guidance', 'CareerGuidanceController@index');
$router->get('/career-guidance/index/{page}', 'CareerGuidanceController@index');
$router->get('/career-guidance/pathways', 'CareerGuidanceController@pathways');
$router->get('/career-guidance/pathways/{page}', 'CareerGuidanceController@pathways');
$router->get('/career-guidance/pathway/{id}', 'CareerGuidanceController@pathway_details');
$router->match('/career-guidance/create-pathway', 'CareerGuidanceController@create_pathway', 'GET|POST');
$router->match('/career-guidance/explore-careers', 'CareerGuidanceController@explore_careers', 'GET');
$router->match('/career-guidance/edit-pathway/{id}', 'CareerGuidanceController@edit_pathway', 'GET|POST');
$router->get('/career-guidance/delete-pathway/{id}', 'CareerGuidanceController@delete_pathway');
$router->get('/career-guidance/scholarships', 'CareerGuidanceController@scholarships');
$router->get('/career-guidance/scholarships/{page}', 'CareerGuidanceController@scholarships');
$router->get('/career-guidance/scholarship/{id}', 'CareerGuidanceController@scholarship_details');
$router->match('/career-guidance/create-scholarship', 'CareerGuidanceController@create_scholarship', 'GET|POST');
$router->match('/career-guidance/find-scholarships', 'CareerGuidanceController@find_scholarships', 'GET');
$router->match('/career-guidance/edit-scholarship/{id}', 'CareerGuidanceController@edit_scholarship', 'GET|POST');
$router->get('/career-guidance/delete-scholarship/{id}', 'CareerGuidanceController@delete_scholarship');
$router->get('/career-guidance/assessments', 'CareerGuidanceController@assessments');
$router->get('/career-guidance/assessments/{page}', 'CareerGuidanceController@assessments');
$router->match('/career-guidance/create-assessment', 'CareerGuidanceController@create_assessment', 'GET|POST');
$router->match('/career-guidance/edit-assessment/{id}', 'CareerGuidanceController@edit_assessment', 'GET|POST');
$router->get('/career-guidance/delete-assessment/{id}', 'CareerGuidanceController@delete_assessment');
$router->match('/career-guidance/take-assessment/{id}', 'CareerGuidanceController@take_assessment', 'GET|POST');

// Reports & Analytics (Counselor Only)
$router->get('/reports', 'ReportsController@index');
$router->get('/reports/api_get_stats', 'ReportsController@api_get_stats');
$router->get('/reports/export', 'ReportsController@export');
$router->get('/reports/refresh', 'ReportsController@refresh');
$router->get('/reports/pdf_export', 'ReportsController@pdf_export');

// Chatbot Route
$router->match('/chatbot/chat', 'ChatbotController@chat', 'GET|POST');

// reCAPTCHA Test Route
$router->get('/auth/recaptcha-test', function() {
    require_once APP_DIR . 'views/auth/recaptcha_test.php';
});

?>