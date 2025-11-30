<?php require_once __DIR__ . '/../includes/header.php'; ?>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-start mb-4">
                <?php 
                    $iconClass = '';
                    $iconBg = '';
                    switch ($resource['type']) {
                        case 'video':
                            $iconClass = 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z';
                            $iconBg = 'bg-red-100 text-red-600';
                            break;
                        case 'audio':
                            $iconClass = 'M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3';
                            $iconBg = 'bg-purple-100 text-purple-600';
                            break;
                        case 'link':
                            $iconClass = 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1';
                            $iconBg = 'bg-blue-100 text-blue-600';
                            break;
                        default: // document
                            $iconClass = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                            $iconBg = 'bg-gray-100 text-gray-600';
                    }
                ?>
                <div class="flex-shrink-0 <?= $iconBg ?> p-3 rounded-lg mr-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconClass ?>"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold"><?= htmlspecialchars($resource['title']) ?></h2>
                    <div class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded-full <?= $iconBg ?>">
                        <?= ucfirst($resource['type']) ?> Resource
                    </div>
                </div>
            </div>
            
            <div class="text-sm text-gray-500 mb-6">
                Uploaded by: <?= htmlspecialchars($resource['counselor_name'] ?? 'Unknown') ?> on <?= date('M d, Y', strtotime($resource['created_at'])) ?>
                <?php if ($resource['views'] > 0): ?>
                    <div class="mt-1">Views: <?= $resource['views'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-6 text-gray-700">
                <?= nl2br(htmlspecialchars($resource['description'])) ?>
            </div>

            <?php if ($resource['type'] === 'video'): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Video Content</h3>
                    <div class="bg-gray-100 rounded-lg p-4">
                        <video controls class="w-full rounded" preload="metadata">
                            <source src="<?= site_url('resources/download/' . $resource['id']) ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            <?php elseif ($resource['type'] === 'audio'): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Audio Content</h3>
                    <div class="bg-gray-100 rounded-lg p-4">
                        <audio controls class="w-full">
                            <source src="<?= site_url('resources/download/' . $resource['id']) ?>" type="audio/mpeg">
                            Your browser does not support the audio tag.
                        </audio>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex items-center space-x-4">
                <?php if ($resource['type'] === 'link'): ?>
                    <a href="<?= htmlspecialchars($resource['file_path']) ?>" target="_blank" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">Open Link</a>
                <?php else: ?>
                    <a href="<?= site_url('resources/download/' . $resource['id']) ?>" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">Download File</a>
                <?php endif; ?>
                <a href="<?= site_url('resources') ?>" class="text-primary hover:text-secondary">Back to list</a>
            </div>
        </div>
    </div>
</body>
</html>