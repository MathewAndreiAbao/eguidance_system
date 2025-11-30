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
        <a href="<?= site_url('career-guidance') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Career Guidance
        </a>
        <div class="flex space-x-2">
            <?php if ($role === 'counselor'): ?>
                <a href="<?= site_url('career-guidance/create-assessment') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                    Create Assessment
                </a>
            <?php endif; ?>
            <h1 class="text-2xl font-bold text-gray-900">Career Assessments</h1>
        </div>
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

    <div class="bg-white shadow rounded-lg p-6">
        <?php if (empty($assessments)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No assessments</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new career assessment.</p>
                <?php if ($role === 'counselor'): ?>
                    <div class="mt-6">
                        <a href="<?= site_url('career-guidance/create-assessment') ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-secondary">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            New Assessment
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($assessments as $assessment): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <h3 class="font-medium text-gray-900 mb-2"><?= htmlspecialchars($assessment['title']) ?></h3>
                            <?php if ($role === 'counselor'): ?>
                                <div class="flex space-x-1">
                                    <a href="<?= site_url('career-guidance/edit-assessment/' . $assessment['id']) ?>" class="text-blue-600 hover:text-blue-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="<?= site_url('career-guidance/delete-assessment/' . $assessment['id']) ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this assessment?')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-3">
                            <p><strong>Type:</strong> 
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                    <?= htmlspecialchars($assessment['type']) ?>
                                </span>
                            </p>
                            
                            <?php if ($role === 'counselor' && !empty($assessment['student_name'])): ?>
                                <p><strong>Student:</strong> <?= htmlspecialchars($assessment['student_name']) ?></p>
                            <?php endif; ?>
                            
                            <p><strong>Date Created:</strong> <?= date('M d, Y', strtotime($assessment['created_at'])) ?></p>
                            
                            <p><strong>Status:</strong> 
                                <?php if (!empty($assessment['score'])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div class="mt-4">
                            <?php if ($role === 'student'): ?>
                                <?php if (empty($assessment['score'])): ?>
                                    <a href="<?= site_url('career-guidance/take-assessment/' . $assessment['id']) ?>" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                                        Take Assessment
                                    </a>
                                <?php else: ?>
                                    <a href="<?= site_url('career-guidance/take-assessment/' . $assessment['id']) ?>" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                        View Results
                                    </a>
                                <?php endif; ?>
                            <?php elseif ($role === 'counselor'): ?>
                                <a href="<?= site_url('career-guidance/edit-assessment/' . $assessment['id']) ?>" class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Manage Assessment
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (isset($pagination)): ?>
                <div class="mt-6 flex justify-center">
                    <?= $pagination ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>