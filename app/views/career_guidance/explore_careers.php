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
        <a href="<?= site_url('career-guidance/pathways') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Career Pathways
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Career Pathway</h1>
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

    <!-- Create Pathway Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">New Career Pathway</h2>
        <p class="text-gray-600 mb-6">
            Create a detailed career pathway to help students understand various career fields, including education requirements and job outlook.
        </p>
        
        <form method="POST" action="<?= site_url('career-guidance/create-pathway') ?>" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Career Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">e.g., Software Engineer, Registered Nurse, Marketing Manager</p>
            </div>
            
            <div>
                <label for="field" class="block text-sm font-medium text-gray-700">Career Field</label>
                <input type="text" name="field" id="field" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">e.g., Technology, Healthcare, Business, Education</p>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Career Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">Provide a detailed overview of this career pathway.</p>
            </div>
            
            <div>
                <label for="education_required" class="block text-sm font-medium text-gray-700">Education Requirements</label>
                <textarea name="education_required" id="education_required" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">List the education requirements for this career (degrees, certifications, etc.).</p>
            </div>
            
            <div>
                <label for="skills_required" class="block text-sm font-medium text-gray-700">Skills Required</label>
                <textarea name="skills_required" id="skills_required" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">List the key skills needed for success in this career.</p>
            </div>
            
            <div>
                <label for="job_outlook" class="block text-sm font-medium text-gray-700">Job Outlook</label>
                <textarea name="job_outlook" id="job_outlook" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                <p class="mt-1 text-sm text-gray-500">Describe the job market outlook for this career (growth projections, demand, etc.).</p>
            </div>
            
            <div>
                <label for="salary_range" class="block text-sm font-medium text-gray-700">Salary Range</label>
                <input type="text" name="salary_range" id="salary_range" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">e.g., $50,000 - $120,000 per year, Entry Level: $40,000+</p>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Career Pathway
                </button>
            </div>
        </form>
    </div>
    
    <!-- Career Pathway Information -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-medium text-gray-900 mb-2">About Career Pathways</h3>
        <p class="text-gray-600 mb-4">
            Career pathways provide students with comprehensive information about various career fields to help them make informed decisions about their future.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-gray-900">Education Requirements</h4>
                <p class="text-sm text-gray-600 mt-1">Detail the formal education, degrees, certifications, or training programs typically required for this career.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Skills & Competencies</h4>
                <p class="text-sm text-gray-600 mt-1">Identify both technical skills and soft skills that are essential for success in this field.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Job Outlook</h4>
                <p class="text-sm text-gray-600 mt-1">Provide information on employment growth projections, job availability, and future demand.</p>
            </div>
            <div>
                <h4 class="font-medium text-gray-900">Salary Information</h4>
                <p class="text-sm text-gray-600 mt-1">Include salary ranges for entry-level, mid-level, and experienced professionals in this field.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>