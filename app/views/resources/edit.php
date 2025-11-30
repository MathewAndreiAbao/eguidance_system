<?php require_once __DIR__ . '/../includes/header.php'; ?>

    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php
            $session = Registry::get_object('session');
            if (!$session) {
                $session = load_class('session', 'libraries');
            }
            $error_msg = $session ? $session->flashdata('error') : null;
        ?>

        <?php if (!empty($error_msg)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= html_escape($error_msg) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Edit Resource</h2>
            <form action="<?= site_url('resources/edit/' . $resource['id']) ?>" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($resource['title']) ?>" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary"><?= htmlspecialchars($resource['description']) ?></textarea>
                </div>

                <?php if ($resource['type'] === 'link'): ?>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Link URL</label>
                        <input type="url" name="resource_link" value="<?= htmlspecialchars($resource['file_path']) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary">
                        <div class="text-xs text-gray-500 mt-2">Current link: <?= htmlspecialchars($resource['file_path']) ?></div>
                    </div>
                <?php else: ?>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Replace File (optional)</label>
                        <input type="file" name="resource_file" accept="*/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary">
                        <div class="text-xs text-gray-500 mt-2">Current file: <?= htmlspecialchars($resource['file_path']) ?></div>
                    </div>
                <?php endif; ?>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">Save</button>
                    <a href="<?= site_url('resources') ?>" class="text-primary hover:text-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>