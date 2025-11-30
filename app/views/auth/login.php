<?php
$page_title = 'Login - EGuidance System';
$form_title = 'Welcome Back';
$form_subtitle = 'Sign in to access your account';
$icon = 'fas fa-sign-in-alt';

ob_start();
?>
<form action="<?= site_url('auth/login') ?>" method="POST">
    <div class="mb-4">
        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
            </div>
            <input type="text" name="username" id="username" class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="Enter your username" required>
        </div>
    </div>

    <div class="mb-6">
        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" name="password" id="password" class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="Enter your password" required>
        </div>
    </div>

    <!-- reCAPTCHA -->
    <?php if (config_item('ENVIRONMENT') !== 'development'): ?>
    <div class="mb-6">
        <div class="g-recaptcha" data-sitekey="<?= config_item('recaptcha_site_key') ?>" data-size="normal"></div>
    </div>
    <?php endif; ?>

    <div class="flex items-center justify-between mb-6">
        <button type="submit" class="btn-primary text-yellow-300 font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>
    </div>
</form>
<?php
$form_content = ob_get_clean();

$footer_links = '
    <p class="text-gray-600 mb-2 text-sm">Don\'t have an account?</p>
    <a href="' . site_url('auth/register') . '" class="form-link inline-flex items-center font-bold transition duration-300">
        <i class="fas fa-user-plus mr-2"></i>Create Account
    </a>
';

// Add reCAPTCHA script
$recaptcha_script = '<script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>';

require_once __DIR__ . '/split_auth_template.php';
?>