<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $success_msg = $session ? $session->flashdata('success') : null;
    $error_msg = $session ? $session->flashdata('error') : null;
?>

<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 flex justify-between items-center">
        <a href="<?= site_url('wellness-forms') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Wellness Forms
        </a>
        <?php if (($role ?? '') === 'counselor'): ?>
            <a href="<?= site_url('wellness-forms/responses/' . $form['id']) ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                View Responses
            </a>
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

    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900"><?= htmlspecialchars($form['title']) ?></h2>
                <p class="text-sm text-gray-600 mt-1">
                    Created <?= date('M d, Y', strtotime($form['created_at'])) ?>
                    <?php if (!empty($form['counselor_name'])): ?> • By <?= htmlspecialchars($form['counselor_name']) ?><?php endif; ?>
                </p>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full <?= ($form['is_active'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' ?>">
                <?= ($form['is_active'] ?? 1) ? 'Open' : 'Closed' ?>
            </span>
        </div>

        <div class="prose max-w-none text-gray-700 mb-6">
            <?= nl2br(htmlspecialchars($form['description'] ?? 'No description provided.')) ?>
        </div>

        <div class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
            <?php if (empty($questions)): ?>
                <p class="text-gray-600">This form does not contain questions yet.</p>
            <?php else: ?>
                <ol class="list-decimal list-inside space-y-4">
                    <?php foreach ($questions as $index => $question): ?>
                        <li class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium text-gray-900"><?= htmlspecialchars($question['question_text']) ?></span>
                                <span class="text-xs uppercase tracking-wide text-gray-500">
                                    <?= $question['question_type'] === 'scale' ? 'Scale' : 'Open text' ?>
                                </span>
                            </div>
                            <?php if ($question['question_type'] === 'scale'): ?>
                                <p class="text-sm text-gray-600">
                                    Answer range: <?= (int) $question['scale_min'] ?> to <?= (int) $question['scale_max'] ?>
                                </p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>

    <?php if (($role ?? '') === 'student'): ?>
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Your Response</h3>

            <?php if (empty($form['is_active'])): ?>
                <p class="text-gray-600">This form is currently closed.</p>
            <?php elseif (!empty($has_submitted)): ?>
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    You already submitted this wellness form. Thank you!
                </div>
            <?php elseif (empty($questions)): ?>
                <p class="text-gray-600">Your counselor is still adding questions. Please check back soon.</p>
            <?php else: ?>
                <form method="POST" action="<?= site_url('wellness-forms/respond/' . $form['id']) ?>" class="space-y-5">
                    <?php foreach ($questions as $question): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                <?= htmlspecialchars($question['question_text']) ?>
                            </label>
                            <?php if ($question['question_type'] === 'scale'): ?>
                                <select name="answers[<?= $question['id'] ?>]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="" disabled selected>Select a value</option>
                                    <?php for ($i = (int) $question['scale_min']; $i <= (int) $question['scale_max']; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            <?php else: ?>
                                <textarea name="answers[<?= $question['id'] ?>]" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Type your answer (optional)"></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md shadow">
                            Submit Response
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

