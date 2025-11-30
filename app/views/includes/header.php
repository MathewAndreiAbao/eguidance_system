<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EGuidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            width: 350px;
            max-height: 500px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        .chatbot-minimized {
            height: 60px !important;
            max-height: 60px !important;
        }
        
        .chatbot-minimized .chatbot-messages,
        .chatbot-minimized .chatbot-input-area {
            display: none !important;
        }
        
        .chatbot-header-bar {
            background: linear-gradient(to right, #145A32, #3CB371);
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-icon-btn {
            background: transparent;
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
            padding: 15px;
            height: 300px;
            overflow-y: auto;
            flex-grow: 1;
        }
        
        .chatbot-input-area {
            display: flex;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        
        .chatbot-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }
        
        .send-btn {
            background: #145A32;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-left: 10px;
            cursor: pointer;
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
            background: #3CB371;
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
        $dashboard_url = ($role == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
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