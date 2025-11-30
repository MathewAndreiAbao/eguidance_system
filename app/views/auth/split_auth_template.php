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
                        darkGreen: '#0d3b20'
                    }
                }
            }
        }
    </script>
    <?= isset($recaptcha_script) ? $recaptcha_script : '' ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0fdf4;
            margin: 0;
            padding: 0;
        }
        
        .left-panel {
            background: linear-gradient(135deg, #145A32 0%, #0d3b20 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .right-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .input-field {
            border: 2px solid #145A32;
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem 0.75rem 3rem;
        }
        
        .input-field:focus {
            border-color: #145A32;
            box-shadow: 0 0 0 3px rgba(20, 90, 50, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #145A32 0%, #0d3b20 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-weight: 600;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }
        
        .form-link {
            color: #FFD700;
            transition: all 0.2s ease;
            font-weight: 600;
            text-decoration: none;
        }
        
        .form-link:hover {
            color: #ffc400;
            text-decoration: underline;
        }
        
        .feature-icon {
            color: #FFD700;
            font-size: 1.5rem;
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                height: auto;
            }
            
            .left-panel, .right-panel {
                width: 100%;
            }
        }
        
        .illustration-container {
            position: relative;
            width: 100%;
            max-width: 300px;
            height: 300px;
            margin: 0 auto;
        }
        
        .illustration-element {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="auth-container flex w-full max-w-6xl h-[600px] rounded-2xl overflow-hidden shadow-2xl floating">
        <!-- Left Panel - Green Section -->
        <div class="left-panel w-1/2 relative">
            <div class="text-white z-10">
                <h1 class="text-2xl md:text-3xl font-bold mb-4 text-center">WELCOME TO YOUR E-GUIDANCE SYSTEM</h1>
                
                <!-- Illustration Area -->
                <div class="my-6 flex justify-center">
                    <div class="illustration-container">
                        <!-- Book Base -->
                        <div class="illustration-element" style="bottom: 0; left: 50%; transform: translateX(-50%); width: 160px; height: 100px;">
                            <div class="w-full h-full bg-white border-2 border-black rounded"></div>
                        </div>
                        
                        <!-- Computer Monitor -->
                        <div class="illustration-element" style="top: 20px; left: 50%; transform: translateX(-50%);">
                            <div class="w-20 h-14 bg-white border-2 border-black rounded flex items-center justify-center">
                                <div class="text-black">
                                    <i class="fas fa-user-tie text-lg"></i>
                                </div>
                            </div>
                            <div class="w-24 h-2 bg-white border-2 border-black rounded-b-lg mx-auto mt-1"></div>
                        </div>
                        
                        <!-- Compass -->
                        <div class="illustration-element" style="top: 80px; left: 30px;">
                            <div class="w-12 h-12 rounded-full border-2 border-black bg-yellow-300 flex items-center justify-center">
                                <i class="fas fa-compass text-black"></i>
                            </div>
                        </div>
                        
                        <!-- Stacked Books -->
                        <div class="illustration-element" style="top: 80px; right: 30px;">
                            <div class="flex flex-col items-end">
                                <div class="w-10 h-4 bg-white border border-black rounded mb-1"></div>
                                <div class="w-12 h-4 bg-white border border-black rounded mb-1"></div>
                                <div class="w-10 h-4 bg-white border border-black rounded"></div>
                            </div>
                        </div>
                        
                        <!-- Apple -->
                        <div class="illustration-element" style="bottom: 120px; left: 50px;">
                            <div class="relative">
                                <div class="w-6 h-6 bg-red-500 rounded-full border border-black"></div>
                                <div class="w-2 h-3 bg-green-600 rounded-r-full absolute -top-1 -right-2"></div>
                            </div>
                        </div>
                        
                        <!-- Gears -->
                        <div class="illustration-element" style="bottom: 100px; right: 60px;">
                            <i class="fas fa-cogs text-2xl text-yellow-300"></i>
                        </div>
                        
                        <!-- Graduation Cap -->
                        <div class="illustration-element" style="top: 120px; left: 50%; transform: translateX(-50%);">
                            <i class="fas fa-graduation-cap text-2xl text-yellow-300"></i>
                        </div>
                        
                        <!-- Speech Bubble -->
                        <div class="illustration-element" style="top: 50px; right: 80px;">
                            <div class="w-10 h-8 bg-white border-2 border-black rounded-full flex items-center justify-center">
                                <i class="fas fa-lightbulb text-yellow-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="text-lg md:text-xl font-semibold mb-6 text-center">GUIDING YOUR PATH TO SUCCESS</p>
            </div>
            
            <!-- Feature Icons -->
            <div class="flex justify-between z-10 mt-auto pt-4">
                <div class="text-center flex-1">
                    <div class="feature-icon mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="text-white text-xs md:text-sm font-medium">Find Mentors</p>
                </div>
                <div class="text-center flex-1">
                    <div class="feature-icon mb-2">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <p class="text-white text-xs md:text-sm font-medium">Explore Careers</p>
                </div>
                <div class="text-center flex-1">
                    <div class="feature-icon mb-2">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <p class="text-white text-xs md:text-sm font-medium">Plan Your Future</p>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - White Section (Form) -->
        <div class="right-panel w-1/2">
            <div class="max-w-sm w-full">
                <!-- Form Icon -->
                <div class="text-center mb-6">
                    <div class="mx-auto bg-darkGreen text-white w-16 h-16 rounded-full flex items-center justify-center mb-4 shadow-lg">
                        <i class="<?= isset($icon) ? $icon : 'fas fa-user-lock' ?> text-2xl text-yellow-300"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-darkGreen mb-2"><?= isset($form_title) ? $form_title : 'Welcome' ?></h1>
                    <p class="text-gray-600 text-sm"><?= isset($form_subtitle) ? $form_subtitle : 'Please sign in to continue' ?></p>
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
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-lg">
                        <p class="text-sm"><i class="fas fa-exclamation-circle mr-2"></i><?= html_escape($error_msg) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($success_msg)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded-lg">
                        <p class="text-sm"><i class="fas fa-check-circle mr-2"></i><?= html_escape($success_msg) ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Form Content -->
                <?= isset($form_content) ? $form_content : '' ?>
                
                <!-- Footer Links -->
                <div class="mt-6 text-center">
                    <?= isset($footer_links) ? $footer_links : '' ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>