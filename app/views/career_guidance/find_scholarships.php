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
        <a href="<?= site_url('career-guidance/scholarships') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Scholarships
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Scholarship</h1>
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

    <!-- Create Scholarship Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">New Scholarship</h2>
        <p class="text-gray-600 mb-6">
            Create a scholarship listing to help students find financial aid opportunities to support their educational goals.
        </p>
        
        <form method="POST" action="<?= site_url('career-guidance/create-scholarship') ?>" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Scholarship Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">e.g., Mindoro State University Excellence Scholarship, CHED Student Grant</p>
            </div>
            
            <div>
                <label for="provider" class="block text-sm font-medium text-gray-700">Provider</label>
                <input type="text" name="provider" id="provider" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">e.g., University Name, Government Agency, Private Organization</p>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">Provide a detailed description of this scholarship opportunity.</p>
            </div>
            
            <div>
                <label for="eligibility_criteria" class="block text-sm font-medium text-gray-700">Eligibility Criteria</label>
                <textarea name="eligibility_criteria" id="eligibility_criteria" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">List the requirements students must meet to be eligible for this scholarship.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="application_deadline" class="block text-sm font-medium text-gray-700">Application Deadline</label>
                    <input type="date" name="application_deadline" id="application_deadline" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">Leave blank if there is no deadline</p>
                </div>
                
                <div>
                    <label for="award_amount" class="block text-sm font-medium text-gray-700">Award Amount</label>
                    <input type="text" name="award_amount" id="award_amount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">e.g., Full tuition, ₱50,000 per semester, Variable</p>
                </div>
            </div>
            
            <div>
                <label for="application_link" class="block text-sm font-medium text-gray-700">Application Link</label>
                <input type="url" name="application_link" id="application_link" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">URL to the official application page (if available)</p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Scholarship
                </button>
            </div>
        </form>
    </div>
    
    <!-- Scholarship Information -->
    <div class="mt-6 bg-purple-50 rounded-lg p-6 border border-purple-200">
        <h3 class="text-lg font-medium text-gray-900 mb-2">About Scholarships</h3>
        <p class="text-gray-600 mb-4">
            Scholarships and financial aid opportunities help students pursue their educational goals by providing financial support.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-gray-900">Eligibility Criteria</h4>
                <p class="text-sm text-gray-600 mt-1">Clearly define the requirements students must meet, such as GPA, field of study, or financial need.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Application Process</h4>
                <p class="text-sm text-gray-600 mt-1">Provide clear instructions on how to apply, including required documents and submission methods.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Award Details</h4>
                <p class="text-sm text-gray-600 mt-1">Specify the amount, duration, and any conditions attached to the scholarship award.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Deadlines</h4>
                <p class="text-sm text-gray-600 mt-1">Include all important dates, including application deadlines and announcement dates.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>