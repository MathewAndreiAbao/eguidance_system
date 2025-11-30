<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $error_msg = $session ? $session->flashdata('error') : null;
    $dashboard_url = ($session && $session->userdata('role') == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4">
        <a href="<?= site_url('career-guidance/assessments') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Assessments
        </a>
    </div>

    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= html_escape($error_msg) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Take Assessment</h1>
            <p class="mt-2 text-gray-600">
                Complete the assessment below. Your results will help guide your career decisions.
            </p>
        </div>

        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($assessment['title']) ?></h2>
            <div class="mt-2 flex items-center text-sm text-gray-600">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                    <?= htmlspecialchars($assessment['type']) ?>
                </span>
                <span class="ml-3">
                    Assigned on <?= date('F j, Y', strtotime($assessment['created_at'])) ?>
                </span>
            </div>
        </div>

        <form method="POST" action="<?= site_url('career-guidance/take-assessment/' . $assessment['id']) ?>">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Score (optional)</label>
                <input type="number" step="0.01" name="score" min="0" max="100"
                       class="shadow appearance-none border rounded w-full max-w-xs py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="e.g., 85.5">
                <p class="mt-1 text-sm text-gray-500">Enter your assessment score if applicable (0-100)</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Results & Recommendations *</label>
                <textarea name="results" required rows="8"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          placeholder="Enter detailed results and career recommendations based on this assessment..."><?= htmlspecialchars($assessment['results'] ?? '') ?></textarea>
                <p class="mt-1 text-sm text-gray-500">Provide detailed results and career recommendations</p>
            </div>

            <div class="flex items-center justify-between">
                <a href="<?= site_url('career-guidance/assessments') ?>" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <?= empty($assessment['score']) ? 'Submit Assessment' : 'Update Assessment' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>