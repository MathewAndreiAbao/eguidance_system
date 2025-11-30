<?php
$current_page = 'resources';
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
        <h2 class="text-2xl font-bold text-gray-900">Counseling Resources</h2>
        <?php if ($role == 'counselor'): ?>
            <a href="<?= site_url('resources/create') ?>" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">Upload Resource</a>
        <?php endif; ?>
    </div>

    <?php if(empty($resources)): ?>
        <div class="bg-white shadow rounded-lg p-6">
            <p class="text-gray-600">No resources found.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach($resources as $resource): ?>
                <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start mb-2">
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
                        <div class="flex-shrink-0 <?= $iconBg ?> p-2 rounded-lg mr-3">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $iconClass ?>"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            <?= htmlspecialchars($resource['title']) ?>
                            <span class="inline-block ml-2 px-2 py-1 text-xs font-semibold rounded-full <?= $iconBg ?>">
                                <?= ucfirst($resource['type']) ?>
                            </span>
                        </h3>
                    </div>
                    <p class="text-gray-600 mb-3 text-sm leading-relaxed">
                        <?= htmlspecialchars(substr($resource['description'], 0, 150)) ?>
                        <?php if (strlen($resource['description']) > 150): ?>
                            ...
                        <?php endif; ?>
                    </p>
                    <div class="text-xs text-gray-500 mb-3">
                        <div>Uploaded by: <?= htmlspecialchars($resource['counselor_name'] ?? 'Unknown') ?></div>
                        <div>On: <?= date('M d, Y', strtotime($resource['created_at'])) ?></div>
                        <?php if ($resource['views'] > 0): ?>
                            <div>Views: <?= $resource['views'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="flex items-center space-x-3">
                            <a href="<?= site_url('resources/view/' . $resource['id']) ?>" class="text-primary hover:text-secondary text-sm font-medium">View</a>
                            <?php if ($resource['type'] === 'link'): ?>
                                <a href="<?= htmlspecialchars($resource['file_path']) ?>" target="_blank" class="text-green-600 hover:text-green-800 text-sm">Open Link</a>
                            <?php else: ?>
                                <a href="<?= site_url('resources/download/' . $resource['id']) ?>" class="text-primary hover:text-secondary text-sm">Download</a>
                            <?php endif; ?>
                        </div>
                        <?php if ($role == 'counselor' && ($resource['counselor_id'] == $session->userdata('user_id'))): ?>
                            <div class="flex space-x-2">
                                <a href="<?= site_url('resources/edit/' . $resource['id']) ?>" class="text-primary hover:text-secondary text-xs">Edit</a>
                                <a href="<?= site_url('resources/delete/' . $resource['id']) ?>" class="text-red-600 hover:text-red-900 text-xs" onclick="return confirm('Delete this resource?')">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($pagination)): ?>
        <div class="mt-6 flex justify-center">
            <?= $pagination ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>