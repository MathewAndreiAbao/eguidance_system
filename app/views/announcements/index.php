<?php
$current_page = 'announcements';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php
        $session = Registry::get_object('session');
        if (!$session) {
            $session = load_class('session', 'libraries');
        }
        $success_msg = $session ? $session->flashdata('success') : null;
        $error_msg = $session ? $session->flashdata('error') : null;
        $role = $session ? $session->userdata('role') : null;
    ?>

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

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Announcements</h2>
        <?php if ($can_create ?? false): ?>
            <a href="<?= site_url('announcements/create') ?>"
               class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">
                Create Announcement
            </a>
        <?php endif; ?>
    </div>

    <?php if(empty($announcements)): ?>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-600">No announcements found.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach($announcements as $announcement): ?>
                <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="<?= site_url('announcements/view/' . $announcement['id']) ?>"
                                   class="text-primary hover:text-secondary">
                                    <?= htmlspecialchars($announcement['title']) ?>
                                </a>
                            </h3>
                            <p class="text-gray-600 mb-3 text-sm leading-relaxed">
                                <?= htmlspecialchars(substr($announcement['content'], 0, 150)) ?>
                                <?php if (strlen($announcement['content']) > 150): ?>
                                    ...
                                <?php endif; ?>
                            </p>
                            <div class="text-xs text-gray-500">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-user mr-1"></i>
                                    <span>By: <?= htmlspecialchars($announcement['counselor_name'] ?? 'Unknown') ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <span>Posted: <?= date('M d, Y', strtotime($announcement['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <a href="<?= site_url('announcements/view/' . $announcement['id']) ?>"
                           class="text-primary hover:text-secondary text-sm font-medium">
                            Read more →
                        </a>
                        <?php if ($role == 'counselor' && ($announcement['counselor_id'] == $session->userdata('user_id'))): ?>
                            <div class="flex space-x-2">
                                <a href="<?= site_url('announcements/edit/' . $announcement['id']) ?>"
                                   class="text-primary hover:text-secondary text-xs">Edit</a>
                                <a href="<?= site_url('announcements/delete/' . $announcement['id']) ?>"
                                   class="text-red-600 hover:text-red-900 text-xs"
                                   onclick="return confirm('Are you sure you want to delete this announcement?')">Delete</a>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>