<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reCAPTCHA Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js?hl=en" async defer></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold text-center mb-6">reCAPTCHA Test</h1>
            
            <form action="#" method="POST">
                <?php if (config_item('ENVIRONMENT') !== 'development'): ?>
                <div class="mb-6">
                    <div class="g-recaptcha" data-sitekey="<?= config_item('recaptcha_site_key') ?>" data-size="normal"></div>
                </div>
                <?php else: ?>
                <div class="mb-6 p-4 bg-yellow-100 rounded-lg">
                    <p class="text-yellow-800">reCAPTCHA is disabled in development mode.</p>
                </div>
                <?php endif; ?>
                
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Test reCAPTCHA
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Site Key: <?= config_item('recaptcha_site_key') ?: 'Not configured' ?>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Secret Key: <?= config_item('recaptcha_secret_key') ? 'Configured' : 'Not configured' ?>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Version: <?= config_item('recaptcha_version') ?: 'v2' ?>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Keys Valid: <?= (config_item('recaptcha_site_key') && config_item('recaptcha_secret_key') && 
                                   config_item('recaptcha_site_key') !== 'YOUR_RECAPTCHA_SITE_KEY' && 
                                   config_item('recaptcha_secret_key') !== 'YOUR_RECAPTCHA_SECRET_KEY') ? 'Yes' : 'No' ?>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Note: If you see "Invalid key type" error, your keys might be for a different reCAPTCHA version.
                    Try changing the version in config.php to 'v3' or 'invisible'.
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Debug Info: 
                    Length of site key: <?= strlen(config_item('recaptcha_site_key')) ?> | 
                    Length of secret key: <?= strlen(config_item('recaptcha_secret_key')) ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>