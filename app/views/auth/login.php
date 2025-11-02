<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EGuidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">EGuidance System</h1>
            
            <?php
                // Resolve session via Registry for views
                $session = Registry::get_object('session');
                if (!$session) {
                    $session = load_class('session', 'libraries');
                }
            ?>
            <?php $error_msg = $session ? $session->flashdata('error') : null; ?>
            <?php if(!empty($error_msg)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= html_escape($error_msg) ?>
                </div>
            <?php endif; ?>

            <?php $success_msg = $session ? $session->flashdata('success') : null; ?>
            <?php if(!empty($success_msg)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= html_escape($success_msg) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/login') ?>" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" id="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Sign In
                    </button>
                    <a href="<?= site_url('auth/register') ?>" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Register
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>