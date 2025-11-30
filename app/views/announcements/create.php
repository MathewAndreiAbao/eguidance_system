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
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Announcement</h2>

            <form action="<?= site_url('announcements/create') ?>" method="POST">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                           placeholder="Enter announcement title">
                </div>

                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea id="content" name="content" rows="8" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                              placeholder="Enter announcement content"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="<?= site_url('announcements') ?>"
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">
                        Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>