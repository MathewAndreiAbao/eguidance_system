<?php require_once __DIR__ . '/../includes/header.php'; ?>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($announcement['title']) ?></h1>

            <div class="border-b border-gray-200 pb-4 mb-6">
                <div class="text-sm text-gray-500">
                    <span>Posted by: <?= htmlspecialchars($announcement['counselor_name'] ?? 'Unknown') ?></span>
                    <span class="mx-2">•</span>
                    <span>Posted on: <?= date('F d, Y \a\t g:i A', strtotime($announcement['created_at'])) ?></span>
                    <?php if ($announcement['updated_at'] != $announcement['created_at']): ?>
                        <span class="mx-2">•</span>
                        <span>Last updated: <?= date('F d, Y \a\t g:i A', strtotime($announcement['updated_at'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="prose max-w-none">
                <p class="text-gray-700 whitespace-pre-line leading-relaxed">
                    <?= nl2br(htmlspecialchars($announcement['content'])) ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
