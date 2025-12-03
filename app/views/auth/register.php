<?php
$page_title = 'Register - EGuidance System';
$form_title = 'Create Account';

ob_start();
?>
<form action="<?= site_url('auth/register') ?>" method="POST">

    <!-- Username -->
    <div class="mb-2">
        <label for="username" class="block text-gray-700 text-sm font-bold mb-1">Username</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
            </div>
            <input type="text" name="username" id="username"
                class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-2 px-2 
                text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300"
                placeholder="Choose a username" required>
        </div>
    </div>

    <!-- Email -->
    <div class="mb-2">
        <label for="email" class="block text-gray-700 text-sm font-bold mb-1">Email</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input type="email" name="email" id="email"
                class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-2 px-2 
                text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300"
                placeholder="your.email@example.com" required>
        </div>
    </div>

    <!-- Password -->
    <div class="mb-2">
        <label for="password" class="block text-gray-700 text-sm font-bold mb-1">Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" name="password" id="password"
                class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-2 px-2 
                text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300"
                placeholder="Create a strong password" required>
        </div>
    </div>

    <!-- Confirm Password -->
    <div class="mb-2">
        <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-1">Confirm Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
            </div>
            <input type="password" name="confirm_password" id="confirm_password"
                class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-2 px-2 
                text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300"
                placeholder="Confirm your password" required>
        </div>
    </div>

    <!-- Role Select -->
    <div class="mb-3">
        <label for="role" class="block text-gray-700 text-sm font-bold mb-1">Role</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user-tag text-gray-400"></i>
            </div>
            <select name="role" id="role"
                class="input-field pl-10 shadow appearance-none border border-primary rounded-lg w-full py-2 px-2 
                text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300"
                required>
                <option value="">Select your role</option>
                <option value="student">Student</option>
                <option value="counselor">Counselor</option>
            </select>
        </div>
    </div>

    <!-- reCAPTCHA -->
    <?php if (config_item('ENVIRONMENT') !== 'development'): ?>
    <div class="mb-3">
        <div class="g-recaptcha" data-sitekey="<?= config_item('recaptcha_site_key') ?>" data-size="normal"></div>
    </div>
    <?php endif; ?>

    <!-- Submit Button -->
    <div class="flex items-center justify-between mb-3">
        <button type="submit"
            class="btn-primary text-yellow-300 font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline 
            transition duration-300 flex items-center justify-center">
            <i class="fas fa-user-plus mr-2"></i>Create Account
        </button>
    </div>

</form>
<?php
$form_content = ob_get_clean();

$footer_links = '
    <p class="text-gray-600 mb-2 text-sm">Already have an account?</p>
    <a href="' . site_url('auth/login') . '" class="form-link inline-flex items-center font-bold transition duration-300">
        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
    </a>
';

$recaptcha_script = '<script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>';

require_once __DIR__ . '/split_auth_template.php';
?>
