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
        <?php if ($role === 'counselor' && $scholarship['created_by'] == $session->userdata('user_id')): ?>
            <div class="flex space-x-2">
                <a href="<?= site_url('career-guidance/edit-scholarship/' . $scholarship['id']) ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="<?= site_url('career-guidance/delete-scholarship/' . $scholarship['id']) ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this scholarship?')">
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
        <div class="bg-gradient-to-r from-purple-500 to-purple-700 px-6 py-8">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white p-3 rounded-full">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-6">
                    <h1 class="text-3xl font-bold text-white"><?= htmlspecialchars($scholarship['title']) ?></h1>
                    <p class="mt-2 text-purple-100">
                        Provided by <span class="font-medium"><?= htmlspecialchars($scholarship['provider']) ?></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="px-6 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Description</h2>
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($scholarship['description'] ?? 'No description available.')) ?>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Eligibility Criteria</h2>
                        <div class="prose max-w-none text-gray-700">
                            <?= nl2br(htmlspecialchars($scholarship['eligibility_criteria'] ?? 'Information not available.')) ?>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Scholarship Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Provider</h4>
                                <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($scholarship['provider']) ?></p>
                            </div>
                            
                            <?php if (!empty($scholarship['award_amount'])): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Award Amount</h4>
                                    <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($scholarship['award_amount']) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($scholarship['application_deadline'])): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Application Deadline</h4>
                                    <p class="mt-1 text-sm <?= strtotime($scholarship['application_deadline']) < time() ? 'text-red-600 font-medium' : 'text-gray-900' ?>">
                                        <?= date('F j, Y', strtotime($scholarship['application_deadline'])) ?>
                                        <?php if (strtotime($scholarship['application_deadline']) < time()): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                                Expired
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Created By</h4>
                                <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($scholarship['counselor_name'] ?? 'Unknown Counselor') ?></p>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Date Created</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    <?= date('F j, Y', strtotime($scholarship['created_at'])) ?>
                                </p>
                            </div>
                            
                            <?php if (!empty($scholarship['updated_at'])): ?>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <?= date('F j, Y', strtotime($scholarship['updated_at'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($scholarship['application_link'])): ?>
                        <div class="bg-purple-50 border border-purple-100 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Apply Now</h3>
                            <p class="text-gray-700 mb-4">
                                Ready to apply for this scholarship? Click the button below to visit the application page.
                            </p>
                            <a href="<?= htmlspecialchars($scholarship['application_link']) ?>" target="_blank" rel="noopener noreferrer" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700">
                                Visit Application Page
                                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>