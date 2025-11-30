<?php
$current_page = 'wellness_forms';
require_once __DIR__ . '/../includes/header.php';
?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $success_msg = $session ? $session->flashdata('success') : null;
    $error_msg = $session ? $session->flashdata('error') : null;
    $role = $session ? $session->userdata('role') : null;
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    <?= ($role ?? '') === 'counselor' ? 'My Wellness Forms' : 'Wellness Check-ins' ?>
                </h2>
                <p class="text-sm text-gray-600">
                    <?= ($role ?? '')
                        ? 'Create wellness surveys to monitor student wellbeing.'
                        : 'Complete these short check-ins prepared by your counselors.' ?>
                </p>
            </div>
            <?php if (($role ?? '') === 'counselor'): ?>
                <a href="<?= site_url('wellness-forms/create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-secondary">
                    Create Wellness Form
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($forms)): ?>
            <p class="text-gray-600">No wellness forms found.</p>
        <?php else: ?>
            <div class="grid gap-6 md:grid-cols-2">
                <?php foreach ($forms as $form): ?>
                    <div class="border rounded-lg p-5 shadow-sm bg-gray-50">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($form['title']) ?></h3>
                                <?php if (($role ?? '') === 'student'): ?>
                                    <p class="text-sm text-gray-600">By <?= htmlspecialchars($form['counselor_name'] ?? 'Counselor') ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full <?= ($form['is_active'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' ?>">
                                <?= ($form['is_active'] ?? 1) ? 'Open' : 'Closed' ?>
                            </span>
                        </div>
                        <p class="text-gray-700 text-sm mb-4">
                            <?= htmlspecialchars(substr($form['description'] ?? 'No description provided.', 0, 160)) ?>
                            <?php if (!empty($form['description']) && strlen($form['description']) > 160): ?>...<?php endif; ?>
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Created <?= date('M d, Y', strtotime($form['created_at'])) ?></span>
                            <?php if (($role ?? '') === 'student'): ?>
                                <?php if (!empty($form['has_submitted'])): ?>
                                    <span class="text-green-600 font-semibold">Submitted</span>
                                <?php else: ?>
                                    <a href="<?= site_url('wellness-forms/view/' . $form['id']) ?>" class="text-primary hover:text-secondary font-semibold">Answer Form →</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="flex items-center space-x-3">
                                    <a href="<?= site_url('wellness-forms/view/' . $form['id']) ?>" class="text-primary hover:text-secondary font-semibold">View</a>
                                    <a href="<?= site_url('wellness-forms/responses/' . $form['id']) ?>" class="text-primary hover:text-secondary font-semibold">Responses</a>
                                    <a href="<?= site_url('wellness-forms/toggle_status/' . $form['id']) ?>" class="text-gray-600 hover:text-gray-900">
                                        <?= ($form['is_active'] ?? 1) ? 'Close' : 'Open' ?>
                                    </a>
                                </div>
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