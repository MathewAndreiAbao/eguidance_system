<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $success_msg = $session ? $session->flashdata('success') : null;
    $error_msg = $session ? $session->flashdata('error') : null;
    $dashboard_url = ($session && $session->userdata('role') == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 flex justify-between items-center">
        <a href="<?= site_url('career-guidance/assessments') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Assessments
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Career Assessment</h1>
    </div>

    <?php if (!empty($success_msg)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= html_escape($success_msg) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= html_escape($error_msg) ?>
        </div>
    <?php endif; ?>

    <!-- Create Assessment Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">New Career Assessment</h2>
        <p class="text-gray-600 mb-6">
            Create a career assessment for a student to help them discover suitable career paths.
        </p>
        
        <form method="POST" action="<?= site_url('career-guidance/create-assessment') ?>" class="space-y-6">
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Select Student</label>
                <select name="student_id" id="student_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Choose a student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>">
                            <?= htmlspecialchars($student['username']) ?> 
                            <?php if (!empty($student['first_name']) || !empty($student['last_name'])): ?>
                                (<?= htmlspecialchars(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Assessment Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">e.g., Career Interest Assessment, Aptitude Test, Personality Profile</p>
            </div>
            
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Assessment Type</label>
                <select name="type" id="type" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Select type</option>
                    <option value="interest">Interest Assessment</option>
                    <option value="aptitude">Aptitude Test</option>
                    <option value="personality">Personality Profile</option>
                </select>
            </div>
            
            <div>
                <label for="score" class="block text-sm font-medium text-gray-700">Score (Optional)</label>
                <input type="number" name="score" id="score" step="0.01" min="0" max="100" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">Numerical score if applicable (0-100)</p>
            </div>
            
            <div>
                <label for="results" class="block text-sm font-medium text-gray-700">Results/Findings</label>
                <textarea name="results" id="results" rows="6" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">Detailed results, interpretations, and career recommendations based on the assessment.</p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Assessment
                </button>
            </div>
        </form>
    </div>
    
    <!-- Assessment Types Information -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-medium text-gray-900 mb-2">About Assessment Types</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h4 class="font-medium text-gray-900">Interest Assessments</h4>
                <p class="text-sm text-gray-600 mt-1">Measure personal interests in various activities, subjects, and work environments.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Aptitude Tests</h4>
                <p class="text-sm text-gray-600 mt-1">Evaluate natural abilities and potential for learning specific skills.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Personality Profiles</h4>
                <p class="text-sm text-gray-600 mt-1">Identify personality traits that influence work preferences and interpersonal styles.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>