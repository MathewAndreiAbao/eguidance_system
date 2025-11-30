<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'EGuidance System' ?></title>
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
    <?= isset($recaptcha_script) ? $recaptcha_script : '' ?>
    <style>
        .floating-form {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(10px);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
        }
        
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(52, 199, 89, 0.4);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .form-link {
            transition: all 0.2s ease;
        }
        
        .form-link:hover {
            transform: translateX(2px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 min-h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-primary text-white py-4 px-6 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">EGuidance System</h1>
            <div class="flex items-center space-x-4">
                <span class="text-msuGold hidden md:block"><i class="fas fa-graduation-cap"></i> Student Counseling Platform</span>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-grow flex items-center justify-center p-4">
        <div class="form-container floating-form w-full max-w-md">
            <div class="p-8">
                <!-- Logo/Icon Section -->
                <div class="text-center mb-8">
                    <div class="mx-auto bg-primary text-white w-20 h-20 rounded-full flex items-center justify-center mb-4 shadow-lg">
                        <i class="<?= isset($icon) ? $icon : 'fas fa-user-lock' ?> text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-primary mb-2"><?= isset($form_title) ? $form_title : 'Welcome' ?></h1>
                    <p class="text-gray-600"><?= isset($form_subtitle) ? $form_subtitle : 'Please sign in to continue' ?></p>
                </div>
                
                <!-- Flash Messages -->
                <?php
                    $session = Registry::get_object('session');
                    if (!$session) {
                        $session = load_class('session', 'libraries');
                    }
                    $error_msg = $session ? $session->flashdata('error') : null;
                    $success_msg = $session ? $session->flashdata('success') : null;
                ?>
                
                <?php if(!empty($error_msg)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg animate-pulse">
                        <p><i class="fas fa-exclamation-circle mr-2"></i><?= html_escape($error_msg) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($success_msg)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                        <p><i class="fas fa-check-circle mr-2"></i><?= html_escape($success_msg) ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Form Content -->
                <?= isset($form_content) ? $form_content : '' ?>
                
                <!-- Footer Links -->
                <div class="mt-8 text-center">
                    <?= isset($footer_links) ? $footer_links : '' ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4">
        <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
            <p>&copy; 2025 EGuidance System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>