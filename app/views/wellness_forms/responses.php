<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
?>

<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 flex justify-between items-center">
        <a href="<?= site_url('wellness-forms') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Wellness Forms
        </a>
        <span class="text-sm text-gray-500">Total responses: <?= count($responses ?? []) ?></span>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($form['title']) ?></h2>
        <p class="text-sm text-gray-600 mb-4">Created <?= date('M d, Y', strtotime($form['created_at'])) ?></p>
        <p class="text-gray-700"><?= nl2br(htmlspecialchars($form['description'] ?? 'No description provided.')) ?></p>
    </div>

    <?php if (empty($responses)): ?>
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded">
            No students have completed this form yet.
        </div>
    <?php else: ?>
        <div class="space-y-5">
            <?php foreach ($responses as $response): ?>
                <div class="bg-white shadow rounded-lg p-5 border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="text-lg font-semibold text-gray-900">
                                <?= htmlspecialchars($response['student_name'] ?? 'Student #' . $response['student_id']) ?>
                            </p>
                            <p class="text-sm text-gray-500">Submitted <?= date('M d, Y g:i A', strtotime($response['submitted_at'])) ?></p>
                        </div>
                    </div>
                    <?php if (empty($response['answers'])): ?>
                        <p class="text-gray-600 text-sm">No answers recorded.</p>
                    <?php else: ?>
                        <dl class="divide-y divide-gray-200">
                            <?php foreach ($response['answers'] as $answer): ?>
                                <div class="py-3">
                                    <dt class="text-sm font-medium text-gray-700"><?= htmlspecialchars($answer['question_text']) ?></dt>
                                    <dd class="mt-1 text-gray-900">
                                        <?php if ($answer['question_type'] === 'scale'): ?>
                                            <span class="inline-flex px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-semibold">
                                                <?= htmlspecialchars($answer['answer_text']) ?>
                                            </span>
                                        <?php else: ?>
                                            <?= nl2br(htmlspecialchars($answer['answer_text'] ?: '—')) ?>
                                        <?php endif; ?>
                                    </dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

