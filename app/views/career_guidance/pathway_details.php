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
        <?php if ($role === 'counselor' && $pathway['created_by'] == $session->userdata('user_id')): ?>
            <div class="flex space-x-2">
                <a href="<?= site_url('career-guidance/edit-pathway/' . $pathway['id']) ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="<?= site_url('career-guidance/delete-pathway/' . $pathway['id']) ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this career pathway?')">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </a>
            </div>
        <?php endif; ?>
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

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 px-6 py-8">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white p-3 rounded-full">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-6">
                    <h1 class="text-3xl font-bold text-white"><?= htmlspecialchars($pathway['title']) ?></h1>
                    <p class="mt-2 text-blue-100">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-400 bg-opacity-20 text-white">
                            <?= htmlspecialchars($pathway['field']) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Overview</h2>
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($pathway['description'] ?? 'No description available.')) ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Education Requirements</h3>
                            <div class="prose max-w-none text-gray-700">
                                <?= nl2br(htmlspecialchars($pathway['education_required'] ?? 'Information not available.')) ?>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills Required</h3>
                            <div class="prose max-w-none text-gray-700">
                                <?= nl2br(htmlspecialchars($pathway['skills_required'] ?? 'Information not available.')) ?>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Outlook</h3>
                            <div class="prose max-w-none text-gray-700">
                                <?= nl2br(htmlspecialchars($pathway['job_outlook'] ?? 'Information not available.')) ?>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Range</h3>
                            <p class="text-gray-700">
                                <?= !empty($pathway['salary_range']) ? htmlspecialchars($pathway['salary_range']) : 'Information not available.' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Career Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Field</h4>
                                <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($pathway['field']) ?></p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Created By</h4>
                                <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($pathway['counselor_name'] ?? 'Unknown Counselor') ?></p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Date Created</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    <?= date('F j, Y', strtotime($pathway['created_at'])) ?>
                                </p>
                            </div>
                            
                            <?php if (!empty($pathway['updated_at'])): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <?= date('F j, Y', strtotime($pathway['updated_at'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Next Steps</h3>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3">Research educational programs in this field</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3">Identify skill-building opportunities</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="ml-3">Connect with professionals in this field</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>