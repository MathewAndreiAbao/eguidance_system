<?php
$current_page = 'feedback';
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

    <?php if (($role ?? '') === 'student'): ?>
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Share Feedback</h2>
                    <p class="text-sm text-gray-600">Let your counselor know how they're doing and how you feel.</p>
                </div>
            </div>
            <form method="POST" action="<?= site_url('feedback/create') ?>" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Counselor</label>
                    <select name="counselor_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" required>
                        <option value="" disabled selected>Select counselor</option>
                        <?php if (!empty($counselors)): ?>
                            <?php foreach ($counselors as $counselor): ?>
                                <option value="<?= $counselor['id'] ?>">
                                    <?= htmlspecialchars($counselor['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" name="subject" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" placeholder="e.g., Recent counseling session" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Rating (optional)</label>
                        <select name="rating" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="">Not rated</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> / 5</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea name="message" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary" placeholder="Share details about your experience..." required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-semibold px-6 py-2 rounded-md shadow">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">
                <?= ($role ?? '') === 'student' ? 'My Feedback' : 'Feedback Received' ?>
            </h2>
        </div>

        <?php if (empty($feedback_list)): ?>
            <p class="text-gray-600">No feedback found.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($feedback_list as $feedback): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900"><?= htmlspecialchars(($role ?? '') === 'student' ? ($feedback['counselor_name'] ?? 'Counselor') : ($feedback['student_name'] ?? 'Student')) ?></h3>
                                <p class="text-sm text-gray-500 mt-1"><?= date('M d, Y g:i A', strtotime($feedback['created_at'])) ?></p>
                            </div>
                            <?php
                                $status = $feedback['status'] ?? 'new';
                                $badgeClasses = [
                                    'new' => 'bg-yellow-100 text-yellow-800',
                                    'in_review' => 'bg-blue-100 text-blue-800',
                                    'resolved' => 'bg-green-100 text-green-800'
                                ];
                            ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClasses[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst(str_replace('_', ' ', $status)) ?>
                            </span>
                        </div>
                        <div class="mt-3">
                            <?php 
                            // Extract subject from feedback content
                            $feedback_content = $feedback['feedback'] ?? '';
                            $subject = 'No Subject';
                            if (preg_match('/^Subject: (.+?)(?:\n|$)/', $feedback_content, $matches)) {
                                $subject = $matches[1];
                            }
                            ?>
                            <h4 class="font-medium text-gray-900"><?= htmlspecialchars($subject) ?></h4>
                            <p class="text-sm text-gray-600 mt-1">
                                <?php 
                                // Extract message body from feedback content
                                $message_body = $feedback_content;
                                if (preg_match('/^Subject: .+?\n\n(.*)/s', $feedback_content, $matches)) {
                                    $message_body = $matches[1];
                                }
                                ?>
                                <?= nl2br(htmlspecialchars(substr($message_body, 0, 120))) ?>
                                <?php if (strlen($message_body) > 120): ?>...<?php endif; ?>
                            </p>
                        </div>
                        <div class="mt-3 flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <?php if ($feedback['rating']): ?>
                                    <span class="font-medium"><?= $feedback['rating'] ?>/5</span> stars
                                <?php else: ?>
                                    No rating
                                <?php endif; ?>
                            </div>
                            <?php if (($role ?? '') === 'counselor'): ?>
                                <form method="POST" action="<?= site_url('feedback/update_status/' . $feedback['id']) ?>" class="flex items-center space-x-2">
                                    <select name="status" class="border-gray-300 rounded-md shadow-sm text-xs focus:ring-primary focus:border-primary">
                                        <option value="new" <?= ($status === 'new') ? 'selected' : '' ?>>New</option>
                                        <option value="in_review" <?= ($status === 'in_review') ? 'selected' : '' ?>>In review</option>
                                        <option value="resolved" <?= ($status === 'resolved') ? 'selected' : '' ?>>Resolved</option>
                                    </select>
                                    <button type="submit" class="text-primary hover:text-secondary text-xs font-medium">Update</button>
                                </form>
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