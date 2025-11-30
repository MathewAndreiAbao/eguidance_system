<?php
$current_page = 'career_guidance';
require_once __DIR__ . '/../includes/header.php';
?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $dashboard_url = ($session && $session->userdata('role') == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Career Guidance</h1>
        <p class="mt-2 text-gray-600">
            Explore Your Career Path. Discover career opportunities, explore different pathways, and find scholarships to support your educational journey. 
            Our career guidance tools will help you make informed decisions about your future.
        </p>
    </div>

    <?php if ($role === 'counselor'): ?>
        <div class="mb-8">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            As a counselor, you can create and manage career pathways, assessments, and scholarships for students.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Main Features -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Career Assessments -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 bg-indigo-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900">Career Assessments</h2>
                        <p class="text-gray-600">Take interest and aptitude assessments to discover careers that match your skills and preferences.</p>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                    <a href="<?= site_url('career-guidance/assessments') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                        View Assessments
                    </a>
                    <?php if ($role === 'counselor'): ?>
                        <a href="<?= site_url('career-guidance/create-assessment') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Assessment
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Career Pathways -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900">Career Pathways</h2>
                        <p class="text-gray-600">Explore detailed information about various career fields, including education requirements and job outlook.</p>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                    <a href="<?= site_url('career-guidance/pathways') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                        Explore Careers
                    </a>
                    <?php if ($role === 'counselor'): ?>
                        <a href="<?= site_url('career-guidance/explore-careers') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Pathway
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Scholarships -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 bg-purple-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-900">Scholarships</h2>
                        <p class="text-gray-600">Find scholarships and financial aid opportunities to support your educational goals.</p>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                    <a href="<?= site_url('career-guidance/scholarships') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                        Find Scholarships
                    </a>
                    <?php if ($role === 'counselor'): ?>
                        <a href="<?= site_url('career-guidance/find-scholarships') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Scholarship
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Recent Items -->
        <div class="space-y-8">
            <!-- Recent Career Pathways -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Career Pathways</h2>
                    <a href="<?= site_url('career-guidance/pathways') ?>" class="text-primary hover:text-secondary text-sm font-medium">
                        View All →
                    </a>
                </div>
                
                <?php if (empty($pathways)): ?>
                    <p class="text-gray-600">No career pathways available yet.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($pathways, 0, 3) as $pathway): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <h3 class="font-medium text-gray-900 mb-1"><?= htmlspecialchars($pathway['title']) ?></h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    <?= htmlspecialchars(substr($pathway['description'] ?? '', 0, 80)) ?>
                                    <?php if (strlen($pathway['description'] ?? '') > 80): ?>...<?php endif; ?>
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= htmlspecialchars($pathway['field']) ?>
                                    </span>
                                    <a href="<?= site_url('career-guidance/pathway/' . $pathway['id']) ?>" class="text-primary hover:text-secondary text-sm font-medium">
                                        Learn More
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Active Scholarships -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Active Scholarships</h2>
                    <a href="<?= site_url('career-guidance/scholarships') ?>" class="text-primary hover:text-secondary text-sm font-medium">
                        View All →
                    </a>
                </div>
                
                <?php if (empty($scholarships)): ?>
                    <p class="text-gray-600">No active scholarships available at this time.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($scholarships, 0, 5) as $scholarship): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <h3 class="font-medium text-gray-900 mb-1"><?= htmlspecialchars($scholarship['title']) ?></h3>
                                <div class="text-sm text-gray-600 mb-2">
                                    <p><strong>Provider:</strong> <?= htmlspecialchars($scholarship['provider']) ?></p>
                                    <?php if (!empty($scholarship['award_amount'])): ?>
                                        <p><strong>Amount:</strong> <?= htmlspecialchars($scholarship['award_amount']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($scholarship['application_deadline'])): ?>
                                        <p><strong>Deadline:</strong> <?= date('M d, Y', strtotime($scholarship['application_deadline'])) ?></p>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= site_url('career-guidance/scholarship/' . $scholarship['id']) ?>" class="inline-flex items-center text-primary hover:text-secondary text-sm font-medium">
                                    View Details →
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>