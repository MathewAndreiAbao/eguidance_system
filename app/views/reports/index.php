<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $success_msg = $session ? $session->flashdata('success') : null;
    $role = $session ? $session->userdata('role') : null;
    $dashboard_url = ($role == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
    $current_page = 'reports';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - EGuidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#145A32',
                        secondary: '#3CB371',
                        msuGold: '#FFD700',
                    }
                }
            }
        }
    </script>
    <style>
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #145A32;
            color: white;
            z-index: 1001;
            transition: all 0.3s ease;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        
        .sidebar-nav {
            padding: 15px 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left: 3px solid #FFD700;
        }
        
        .nav-item i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }
        
        .nav-item span {
            font-size: 15px;
            font-weight: 500;
        }
        
        .main-content {
            margin-left: 260px;
            transition: all 0.3s ease;
        }
        
        /* Stats Card Animation */
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* Progress Bar Animation */
        .progress-bar {
            height: 100%;
            transition: width 1s ease-in-out;
        }
        
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .data-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        
        .data-table tr:hover {
            background-color: #f7fafc;
        }
        
        /* Rating Stars */
        .rating-stars {
            color: #f6ad55;
        }
        
        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .sidebar {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h1 {
                font-size: 0;
            }
            
            .nav-item span {
                display: none;
            }
            
            .nav-item i {
                margin-right: 0;
                font-size: 20px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .grid-cols-1,
            .grid-cols-2,
            .grid-cols-4 {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 250px;
            }
        }
        
        /* Custom Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .delay-1 {
            animation-delay: 0.1s;
        }
        
        .delay-2 {
            animation-delay: 0.2s;
        }
        
        .delay-3 {
            animation-delay: 0.3s;
        }
        
        .delay-4 {
            animation-delay: 0.4s;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>EGuidance System</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= $dashboard_url ?>" class="nav-item<?= (isset($current_page) && $current_page == 'dashboard') ? ' active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= site_url('appointments') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'appointments') ? ' active' : '' ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Appointments</span>
            </a>
            <a href="<?= site_url('resources') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'resources') ? ' active' : '' ?>">
                <i class="fas fa-book"></i>
                <span>Resources</span>
            </a>
            <a href="<?= site_url('career-guidance') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'career_guidance') ? ' active' : '' ?>">
                <i class="fas fa-graduation-cap"></i>
                <span>Career Guidance</span>
            </a>
            <a href="<?= site_url('feedback') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'feedback') ? ' active' : '' ?>">
                <i class="fas fa-comment"></i>
                <span>Feedback</span>
            </a>
            <a href="<?= site_url('wellness-forms') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'wellness_forms') ? ' active' : '' ?>">
                <i class="fas fa-heartbeat"></i>
                <span>Wellness Forms</span>
            </a>
            <a href="<?= site_url('announcements') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'announcements') ? ' active' : '' ?>">
                <i class="fas fa-bullhorn"></i>
                <span>Announcements</span>
            </a>
            <a href="<?= site_url('maps') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'maps') ? ' active' : '' ?>">
                <i class="fas fa-map-marker-alt"></i>
                <span>Maps</span>
            </a>
            <a href="<?= site_url('reports') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'reports') ? ' active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports & Analytics</span>
            </a>
            <a href="<?= site_url('profile') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'profile') ? ' active' : '' ?>">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="<?= site_url('auth/logout') ?>" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 no-print">
                <div></div>
                <div class="flex flex-wrap gap-2">
                    <a href="<?= site_url('reports/export?format=json') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export JSON
                    </a>
                    <a href="<?= site_url('reports/export?format=csv') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export CSV
                    </a>
                    <a href="<?= site_url('reports/export?format=pdf') ?>" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export PDF
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?= html_escape($success_msg) ?>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="bg-white shadow rounded-lg p-6 mb-6 fade-in-up">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
                        <p class="mt-2 text-sm text-gray-600">Comprehensive overview of your counseling activities and student engagement</p>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <p class="text-xs text-gray-500">Generated: <?= date('F d, Y - h:i A') ?></p>
                        <p class="text-xs text-gray-500">Report ID: <?= uniqid() ?></p>
                    </div>
                </div>
            </div>

            <!-- Overview Statistics Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow rounded-lg fade-in-up delay-1">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-white truncate opacity-75">Total Students</dt>
                                    <dd class="text-3xl font-bold text-white"><?= $overview['total_students'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-700 px-5 py-3">
                        <div class="text-sm text-white opacity-75">
                            <span class="inline-flex items-center">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                +12% from last month
                            </span>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow rounded-lg fade-in-up delay-2">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-white truncate opacity-75">Total Appointments</dt>
                                    <dd class="text-3xl font-bold text-white"><?= $overview['total_appointments'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-700 px-5 py-3">
                        <div class="text-sm text-white opacity-75">
                            <span class="inline-flex items-center">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                +8% from last month
                            </span>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-gradient-to-br from-yellow-500 to-yellow-600 overflow-hidden shadow rounded-lg fade-in-up delay-3">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-white truncate opacity-75">Total Feedback</dt>
                                    <dd class="text-3xl font-bold text-white"><?= $overview['total_feedback'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-700 px-5 py-3">
                        <div class="text-sm text-white opacity-75">
                            <span class="inline-flex items-center">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                +5% from last month
                            </span>
                        </div>
                    </div>
                </div>

                <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow rounded-lg fade-in-up delay-4">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-white truncate opacity-75">Wellness Forms</dt>
                                    <dd class="text-3xl font-bold text-white"><?= $overview['total_wellness_forms'] ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-700 px-5 py-3">
                        <div class="text-sm text-white opacity-75">
                            <span class="inline-flex items-center">
                                <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                +3% from last month
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Appointments by Status -->
                <div class="bg-white shadow rounded-lg p-6 fade-in-up delay-1">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Appointments by Status</h3>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i> Distribution of appointments by current status
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="appointmentsStatusChart"></canvas>
                    </div>
                </div>

                <!-- Feedback Rating Distribution -->
                <div class="bg-white shadow rounded-lg p-6 fade-in-up delay-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Feedback Rating Distribution</h3>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i> Student satisfaction ratings
                        </div>
                    </div>
                    <div class="text-center mb-4">
                        <span class="text-4xl font-bold text-gray-900"><?= $feedback_stats['average_rating'] ?></span>
                        <span class="text-lg text-gray-600">/5.0</span>
                        <div class="mt-1 flex justify-center">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($feedback_stats['average_rating'])): ?>
                                    <i class="fas fa-star rating-stars"></i>
                                <?php elseif ($i - 0.5 <= $feedback_stats['average_rating']): ?>
                                    <i class="fas fa-star-half-alt rating-stars"></i>
                                <?php else: ?>
                                    <i class="far fa-star rating-stars"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Average Rating</p>
                    </div>
                    <div class="chart-container">
                        <canvas id="ratingDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Appointments Trend -->
            <div class="bg-white shadow rounded-lg p-6 mb-6 fade-in-up delay-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Appointments Trend (Last 6 Months)</h3>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle"></i> Monthly appointment volume
                    </div>
                </div>
                <div class="chart-container" style="height: 350px;">
                    <canvas id="appointmentsTrendChart"></canvas>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Feedback Status -->
                <div class="bg-white shadow rounded-lg p-6 fade-in-up delay-1">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Feedback Status Breakdown</h3>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i> Current feedback processing status
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="feedbackStatusChart"></canvas>
                    </div>
                </div>

                <!-- Top Active Students -->
                <div class="bg-white shadow rounded-lg p-6 fade-in-up delay-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Most Active Students</h3>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i> Students with most appointments
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <?php if (empty($top_students)): ?>
                            <div class="flex items-center justify-center h-64">
                                <p class="text-gray-500 text-center py-4">No student data available yet.</p>
                            </div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Appointments</th>
                                        <th>Activity Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_students as $student): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= html_escape($student['username']) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                    <?= $student['appointment_count'] ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-blue-600 h-2.5 rounded-full progress-bar" 
                                                         style="width: <?= min(100, ($student['appointment_count'] / max(1, array_column($top_students, 'appointment_count')[0])) * 100) ?>%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Wellness Forms Response Stats -->
            <div class="bg-white shadow rounded-lg p-6 mb-6 fade-in-up delay-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Wellness Forms Response Statistics</h3>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle"></i> Student engagement with wellness forms
                    </div>
                </div>
                <?php if (empty($wellness_stats)): ?>
                    <div class="flex items-center justify-center h-64">
                        <p class="text-gray-500 text-center py-4">No wellness form data available yet.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Form Title</th>
                                    <th>Responses</th>
                                    <th>Engagement Rate</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wellness_stats as $form): ?>
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <?= html_escape($form['title']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <?= $form['response_count'] ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-green-600 h-2.5 rounded-full progress-bar" 
                                                     style="width: <?= min(100, $form['response_count'] * 10) ?>%"></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if ($form['response_count'] > 20): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    High Engagement
                                                </span>
                                            <?php elseif ($form['response_count'] > 10): ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Moderate
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Low
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>




        </div>
    </div>

    <script>
    // Initialize all charts
    document.addEventListener('DOMContentLoaded', function() {
        // Appointments by Status Chart (Doughnut)
        const appointmentsStatusCtx = document.getElementById('appointmentsStatusChart').getContext('2d');
        new Chart(appointmentsStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        <?= $appointments_by_status['pending'] ?>,
                        <?= $appointments_by_status['confirmed'] ?>,
                        <?= $appointments_by_status['completed'] ?>,
                        <?= $appointments_by_status['cancelled'] ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 205, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // Feedback Rating Distribution Chart (Bar)
        const ratingLabels = [];
        const ratingCounts = [];
        for (let i = 1; i <= 5; i++) {
            ratingLabels.push(i + ' Star' + (i > 1 ? 's' : ''));
            ratingCounts.push(<?= json_encode($feedback_stats['rating_distribution']) ?>[i] || 0);
        }
        
        const ratingDistributionCtx = document.getElementById('ratingDistributionChart').getContext('2d');
        new Chart(ratingDistributionCtx, {
            type: 'bar',
            data: {
                labels: ratingLabels,
                datasets: [{
                    label: 'Number of Ratings',
                    data: ratingCounts,
                    backgroundColor: 'rgba(153, 102, 255, 0.8)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Appointments Trend Chart (Line)
        const trendLabels = <?= json_encode(array_column($appointments_trend, 'month')) ?>;
        const trendCounts = <?= json_encode(array_column($appointments_trend, 'count')) ?>;
        
        const appointmentsTrendCtx = document.getElementById('appointmentsTrendChart').getContext('2d');
        new Chart(appointmentsTrendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Appointments',
                    data: trendCounts,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Feedback Status Chart (Pie)
        const feedbackStatusCtx = document.getElementById('feedbackStatusChart').getContext('2d');
        new Chart(feedbackStatusCtx, {
            type: 'pie',
            data: {
                labels: ['New', 'In Review', 'Resolved'],
                datasets: [{
                    data: [
                        <?= $feedback_stats['by_status']['new'] ?>,
                        <?= $feedback_stats['by_status']['in_review'] ?>,
                        <?= $feedback_stats['by_status']['resolved'] ?>
                    ],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderColor: [
                        'rgba(239, 68, 68, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(34, 197, 94, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // Initialize progress bars with animation
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    });
    </script>

</body>
</html>