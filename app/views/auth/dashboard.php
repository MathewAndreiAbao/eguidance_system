<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .ai-chatbot-wrapper {
            position: fixed;
            bottom: 90px;
            right: 20px;
            z-index: 1000;
            width: 350px;
            max-height: 500px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .chatbot-header-bar {
            background: linear-gradient(135deg, #145A32 0%, #3CB371 100%);
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .header-icon-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chatbot-logo {
            font-size: 20px;
        }

        .chatbot-name {
            font-weight: bold;
            font-size: 16px;
        }

        .chatbot-messages {
            background: white;
            height: 350px;
            padding: 15px;
            overflow-y: auto;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .chatbot-input-area {
            background: white;
            padding: 15px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e2e8f0;
            border-top: none;
            display: flex;
        }

        .chatbot-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            outline: none;
        }

        .send-btn {
            background: #145A32;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            border-radius: 20px;
            cursor: pointer;
        }

        .chatbot-minimized {
            height: auto;
        }

        .chatbot-minimized .chatbot-messages,
        .chatbot-minimized .chatbot-input-area {
            display: none;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        
        .animate-bounce {
            animation: bounce 1s infinite;
        }
        
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
        
        /* Navigation Icons */
        .nav-icons {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 999;
            display: flex;
            gap: 15px;
        }
        
        .nav-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #145A32;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .nav-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .nav-icon i {
            font-size: 20px;
        }
        
        /* Chatbot Popup Icon */
        .chatbot-popup-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #145A32;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1001;
        }
        
        .chatbot-popup-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            background: #3CB371;
        }
        
        .chatbot-popup-icon i {
            font-size: 20px;
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
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php
        $session = Registry::get_object('session');
        if (!$session) {
            $session = load_class('session', 'libraries');
        }
        $role = $session ? $session->userdata('role') : null;
        $name = $session ? $session->userdata('username') : null;
        $dashboard_url = ($role == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
        $current_page = 'dashboard';
    ?>
    
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
            <?php if ($role == 'counselor'): ?>
                <a href="<?= site_url('reports') ?>" class="nav-item<?= (isset($current_page) && $current_page == 'reports') ? ' active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports & Analytics</span>
                </a>
            <?php endif; ?>
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

        <?php if (!empty($success_msg)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= html_escape($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if($role == 'student'): ?>

            <div class="space-y-6">
                <!-- Welcome Section -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome, <?= htmlspecialchars($profile['name'] ?? $name) ?>!</h1>
                    <p class="mt-2 text-gray-600">Here's what's happening with your counseling journey today.</p>
                </div>

                <!-- Motivational Quote -->
                <?php if (!empty($motivational_quote)): ?>
                    <div class="bg-gradient-to-r from-primary to-secondary rounded-lg p-6 text-white mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <svg class="h-8 w-8 text-msuGold" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Motivational Quote</h3>
                                <p class="italic">"<?= htmlspecialchars($motivational_quote) ?>"</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 ml-4">Appointments</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Schedule and manage your counseling sessions.
                        </p>
                        <a href="<?= site_url('appointments') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                            View Appointments
                        </a>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 ml-4">Resources</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Access helpful counseling resources and materials.
                        </p>
                        <a href="<?= site_url('resources') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                            Browse Resources
                        </a>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-purple-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 ml-4">Career Guidance</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Explore career pathways and opportunities.
                        </p>
                        <a href="<?= site_url('career-guidance') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                            Explore Careers
                        </a>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <?php if (!empty($todays_appointments)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Today's Appointments</h2>
                            <a href="<?= site_url('appointments') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($todays_appointments as $apt): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= htmlspecialchars($apt['counselor_name'] ?? 'Unknown Counselor') ?></h3>
                                            <p class="text-sm text-gray-500 mt-1"><?= date('h:i A', strtotime($apt['time'])) ?></p>
                                        </div>
                                        <?php
                                            switch ($apt['status']) {
                                                case 'confirmed': $statusClass = 'bg-green-100 text-green-800'; break;
                                                case 'pending': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'cancelled': $statusClass = 'bg-red-100 text-red-800'; break;
                                                default: $statusClass = 'bg-gray-100 text-gray-800';
                                            }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($apt['status']) ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <?= htmlspecialchars(substr($apt['reason'] ?? $apt['purpose'] ?? '', 0, 60)) ?>
                                        <?php if (strlen($apt['reason'] ?? $apt['purpose'] ?? '') > 60): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Upcoming Appointments -->
                <?php if (!empty($appointments)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Your Upcoming Appointments</h2>
                            <a href="<?= site_url('appointments') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($appointments as $apt): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= htmlspecialchars($apt['counselor_name'] ?? 'Unknown Counselor') ?></h3>
                                            <p class="text-sm text-gray-500 mt-1"><?= date('M d, Y', strtotime($apt['date'])) ?> at <?= date('h:i A', strtotime($apt['time'])) ?></p>
                                        </div>
                                        <?php
                                            switch ($apt['status']) {
                                                case 'confirmed': $statusClass = 'bg-green-100 text-green-800'; break;
                                                case 'pending': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'cancelled': $statusClass = 'bg-red-100 text-red-800'; break;
                                                default: $statusClass = 'bg-gray-100 text-gray-800';
                                            }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($apt['status']) ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <?= htmlspecialchars(substr($apt['reason'] ?? $apt['purpose'] ?? '', 0, 60)) ?>
                                        <?php if (strlen($apt['reason'] ?? $apt['purpose'] ?? '') > 60): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recent Resources -->
                <?php if (!empty($recent_resources)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Recent Resources</h2>
                            <a href="<?= site_url('resources') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($recent_resources as $res): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h3 class="font-medium text-gray-900"><?= htmlspecialchars($res['title']) ?></h3>
                                    <p class="text-xs text-gray-500 mt-1">By <?= htmlspecialchars($res['counselor_name'] ?? 'Unknown') ?> • <?= date('M d, Y', strtotime($res['created_at'])) ?></p>
                                    <a href="<?= site_url('resources/view/' . $res['id']) ?>" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">Read More →</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($pagination)): ?>
                            <div class="mt-6 flex justify-center">
                                <?= $pagination ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Wellness Section -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Wellness Tracker</h2>
                        <a href="<?= site_url('wellness-forms') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-medium text-blue-800">Mental Health Check-in</h3>
                            <p class="text-sm text-blue-600 mt-1">Complete your weekly mental health assessment</p>
                            <a href="<?= site_url('wellness-forms') ?>" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">Start Assessment →</a>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-medium text-green-800">Stress Management</h3>
                            <p class="text-sm text-green-600 mt-1">Access stress relief techniques and resources</p>
                            <a href="<?= site_url('resources') ?>" class="mt-2 inline-block text-sm text-green-600 hover:text-green-800">Learn More →</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>

            <div class="space-y-6">
                <!-- Welcome Section -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome, Counselor <?= htmlspecialchars($profile['name'] ?? $name) ?>!</h1>
                    <p class="mt-2 text-gray-600">Here's an overview of your counseling activities.</p>
                </div>

                <!-- Motivational Quote -->
                <?php if (!empty($motivational_quote)): ?>
                    <div class="bg-gradient-to-r from-primary to-secondary rounded-lg p-6 text-white mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <svg class="h-8 w-8 text-msuGold" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Motivational Quote</h3>
                                <p class="italic">"<?= htmlspecialchars($motivational_quote) ?>"</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Today's Appointments</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900"><?= count($todays_appointments ?? []) ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Appointments</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900"><?= count($appointments ?? []) ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Appointments</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900"><?= count($pending_appointments ?? []) ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Available Resources</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900"><?= count($recent_resources ?? []) ?></div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 ml-4">Appointments</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Manage student appointments and schedules.
                        </p>
                        
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-green-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 ml-4">Resources</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Create and manage counseling resources.
                        </p>
                        <a href="<?= site_url('resources/create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                            Add Resource
                        </a>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 bg-purple-100 p-3 rounded-full">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 ml-4">Career Guidance</h3>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Manage career pathways and scholarships.
                        </p>
                        <a href="<?= site_url('career-guidance') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                            Manage Careers
                        </a>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <?php if (!empty($todays_appointments)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Today's Appointments</h2>
                            <a href="<?= site_url('appointments/manage') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($todays_appointments as $apt): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= htmlspecialchars($apt['student_name'] ?? 'Unknown Student') ?></h3>
                                            <p class="text-sm text-gray-500 mt-1"><?= date('h:i A', strtotime($apt['time'])) ?></p>
                                        </div>
                                        <?php
                                            switch ($apt['status']) {
                                                case 'confirmed': $statusClass = 'bg-green-100 text-green-800'; break;
                                                case 'pending': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'cancelled': $statusClass = 'bg-red-100 text-red-800'; break;
                                                default: $statusClass = 'bg-gray-100 text-gray-800';
                                            }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($apt['status']) ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <?= htmlspecialchars(substr($apt['reason'] ?? $apt['purpose'] ?? '', 0, 60)) ?>
                                        <?php if (strlen($apt['reason'] ?? $apt['purpose'] ?? '') > 60): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Pending Appointments -->
                <?php if (!empty($pending_appointments)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Pending Appointments</h2>
                            <a href="<?= site_url('appointments/manage') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($pending_appointments as $apt): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h3 class="font-medium text-gray-900"><?= htmlspecialchars($apt['student_name'] ?? 'Unknown Student') ?></h3>
                                    <p class="text-sm text-gray-500 mt-1"><?= date('M d, Y', strtotime($apt['date'])) ?> at <?= date('h:i A', strtotime($apt['time'])) ?></p>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <?= htmlspecialchars(substr($apt['reason'] ?? $apt['purpose'] ?? '', 0, 60)) ?>
                                        <?php if (strlen($apt['reason'] ?? $apt['purpose'] ?? '') > 60): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recent Appointments -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900">Recent Appointments</h2>
                        <a href="<?= site_url('appointments/manage') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                    </div>
                    <?php if (empty($all_appointments)): ?>
                        <p class="text-gray-600">No recent appointments.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach (array_slice($all_appointments, 0, 5) as $apt): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= htmlspecialchars($apt['student_name'] ?? 'Unknown Student') ?></h3>
                                            <p class="text-sm text-gray-500 mt-1"><?= date('M d, Y', strtotime($apt['date'])) ?> at <?= date('h:i A', strtotime($apt['time'])) ?></p>
                                        </div>
                                        <?php
                                            switch ($apt['status']) {
                                                case 'confirmed': $statusClass = 'bg-green-100 text-green-800'; break;
                                                case 'pending': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'cancelled': $statusClass = 'bg-red-100 text-red-800'; break;
                                                default: $statusClass = 'bg-gray-100 text-gray-800';
                                            }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                            <?= ucfirst($apt['status']) ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <?= htmlspecialchars(substr($apt['reason'] ?? $apt['purpose'] ?? '', 0, 60)) ?>
                                        <?php if (strlen($apt['reason'] ?? $apt['purpose'] ?? '') > 60): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Your Resources -->
                <?php if (!empty($my_resources)): ?>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Your Resources</h2>
                            <a href="<?= site_url('resources') ?>" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($my_resources as $res): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h3 class="font-medium text-gray-900"><?= htmlspecialchars($res['title']) ?></h3>
                                    <p class="text-xs text-gray-500 mt-1">Created <?= date('M d, Y', strtotime($res['created_at'])) ?></p>
                                    <a href="<?= site_url('resources/view/' . $res['id']) ?>" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">View Resource →</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (isset($pagination)): ?>
                            <div class="mt-6 flex justify-center">
                                <?= $pagination ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                

            </div>

        <?php endif; ?>
    </div>


    
    <!-- Chatbot Icon - only appears for student roles -->
    <?php if($role == 'student'): ?>
        <!-- AI Chatbot -->
        <div id="ai-chatbot-container" class="ai-chatbot-wrapper">
            <div class="chatbot-header-bar">
                <button class="header-icon-btn" onclick="toggleChatbotMinimize()">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="header-title">
                    <div class="chatbot-logo">
                        <i class="fas fa-robot"></i>
                    </div>
                    <span class="chatbot-name">AI ChatBot</span>
                </div>
                <button class="header-icon-btn" onclick="toggleChatbotMinimize()">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <div class="chatbot-messages" id="chatbot-messages">
                <div class="mb-3">
                    <div class="bg-blue-100 text-blue-800 rounded-lg p-3 inline-block max-w-xs">
                        Hello <?= htmlspecialchars($profile['name'] ?? $name) ?>! I'm your AI counseling assistant. How can I help you today?
                    </div>
                </div>
            </div>
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbot-input" placeholder="Type your message here..." onkeypress="handleKeyPress(event)">
                <button class="send-btn" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
        
        <div class="chatbot-popup-icon" onclick="toggleChatbot()" title="AI ChatBot">
            <i class="fas fa-robot"></i>
        </div>
        
        <script>
            function toggleChatbot() {
                const container = document.getElementById('ai-chatbot-container');
                if (container.style.display === 'none') {
                    // Show the chatbot and remove minimized class to show full conversation
                    container.style.display = 'flex';
                    container.classList.remove('chatbot-minimized');
                } else {
                    // Hide the chatbot
                    container.style.display = 'none';
                }
            }
            
            function toggleChatbotMinimize() {
                const container = document.getElementById('ai-chatbot-container');
                container.classList.toggle('chatbot-minimized');
            }
            
            function handleKeyPress(event) {
                if (event.key === 'Enter') {
                    sendMessage();
                }
            }
            
            async function sendMessage() {
                const input = document.getElementById('chatbot-input');
                const message = input.value.trim();
                
                if (message) {
                    // Add user message to chat
                    addMessageToChat(message, 'user');
                    input.value = '';
                    
                    // Show typing indicator
                    const typingIndicator = document.createElement('div');
                    typingIndicator.id = 'typing-indicator';
                    typingIndicator.className = 'mb-3';
                    typingIndicator.innerHTML = `
                        <div class="text-left">
                            <div class="bg-blue-100 text-blue-800 rounded-lg p-3 inline-block max-w-xs">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('chatbot-messages').appendChild(typingIndicator);
                    document.getElementById('chatbot-messages').scrollTop = document.getElementById('chatbot-messages').scrollHeight;
                    
                    try {
                        // Call the actual chatbot API
                        const response = await fetch('<?= site_url('chatbot/chat') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                message: message
                            })
                        });
                        
                        const data = await response.json();
                        
                        // Remove typing indicator
                        if (document.getElementById('typing-indicator')) {
                            document.getElementById('typing-indicator').remove();
                        }
                        
                        // Add AI response to chat
                        addMessageToChat(data.response, 'ai');
                    } catch (error) {
                        // Remove typing indicator
                        if (document.getElementById('typing-indicator')) {
                            document.getElementById('typing-indicator').remove();
                        }
                        
                        // Show error message
                        addMessageToChat('Sorry, I am currently unable to process your request. Please try again later.', 'ai');
                    }
                }
            }
            
            function addMessageToChat(message, sender) {
                const chatMessages = document.getElementById('chatbot-messages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'mb-3';
                
                if (sender === 'user') {
                    messageDiv.innerHTML = `
                        <div class="text-right">
                            <div class="bg-primary text-white rounded-lg p-3 inline-block max-w-xs">
                                ${message}
                            </div>
                        </div>
                    `;
                } else {
                    messageDiv.innerHTML = `
                        <div class="text-left">
                            <div class="bg-blue-100 text-blue-800 rounded-lg p-3 inline-block max-w-xs">
                                ${message}
                            </div>
                        </div>
                    `;
                }
                
                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        </script>
    <?php endif; ?>
    
    </div> <!-- Close main-content -->
</body>
</html>