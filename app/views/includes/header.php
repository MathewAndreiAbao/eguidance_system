<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <?php
                        $session = Registry::get_object('session');
                        if (!$session) {
                            $session = load_class('session', 'libraries');
                        }
                        $dashboard_url = ($session && $session->userdata('role') == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
                        ?>
                        <a href="<?= $dashboard_url ?>" class="text-xl font-bold text-gray-800">Dashboard</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="/index.php/profile" class="text-gray-700 hover:text-gray-900 px-3 py-2">Profile</a>
                    <a href="/index.php/auth/logout" class="text-gray-700 hover:text-gray-900 px-3 py-2">Logout</a>
                </div>
            </div>
        </div>
    </nav>
