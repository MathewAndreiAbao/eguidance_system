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
            <h2 class="text-2xl font-bold mb-4">Upload Counseling Resource</h2>
            <form action="<?= site_url('resources/create') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                    <input type="text" name="title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Resource Type</label>
                    <div class="flex space-x-4 mb-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="resource_type" value="file" checked class="resource-type-radio">
                            <span class="ml-2">Upload File</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="resource_type" value="link" class="resource-type-radio">
                            <span class="ml-2">Other Link</span>
                        </label>
                    </div>
                    <div id="file-upload-section">
                        <input type="file" name="resource_file" accept="*/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary">
                        <p class="mt-1 text-sm text-gray-500">Supported formats: PDF, DOC, MP4, MP3, and more</p>
                    </div>
                    <div id="link-section" class="hidden">
                        <input type="url" name="resource_link" placeholder="https://example.com/resource" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-primary focus:border-primary">
                        <p class="mt-1 text-sm text-gray-500">Enter the full URL to the external resource</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">Upload</button>
                    <a href="<?= site_url('resources') ?>" class="text-primary hover:text-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<script>
    // Toggle between file upload and link input
    document.querySelectorAll('.resource-type-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('file-upload-section').classList.add('hidden');
            document.getElementById('link-section').classList.add('hidden');
            
            if (this.value === 'file') {
                document.getElementById('file-upload-section').classList.remove('hidden');
            } else {
                document.getElementById('link-section').classList.remove('hidden');
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>