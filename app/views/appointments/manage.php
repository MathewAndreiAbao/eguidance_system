<?php
$current_page = 'appointments';
require_once __DIR__ . '/../includes/header.php';
?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $success_msg = $session ? $session->flashdata('success') : null;
    $error_msg = $session ? $session->flashdata('error') : null;
    $filters = $filters ?? ['search' => '', 'sort_by' => 'date', 'sort_dir' => 'asc'];
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php if (!empty($success_msg)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="<?= site_url('appointments/manage') ?>" class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="<?= html_escape($filters['search'] ?? '') ?>" placeholder="Search by student, purpose, status..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                <select name="sort_by" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary">
                    <?php
                        $sortOptions = [
                            'date' => 'Date',
                            'time' => 'Time',
                            'student' => 'Student',
                            'status' => 'Status',
                            'created' => 'Created At'
                        ];
                    ?>
                    <?php foreach ($sortOptions as $value => $label): ?>
                        <option value="<?= $value ?>" <?= (($filters['sort_by'] ?? 'date') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                <select name="sort_dir" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary">
                    <option value="asc" <?= (($filters['sort_dir'] ?? 'asc') === 'asc') ? 'selected' : '' ?>>Ascending</option>
                    <option value="desc" <?= (($filters['sort_dir'] ?? 'asc') === 'desc') ? 'selected' : '' ?>>Descending</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center space-x-3">
            <button type="submit" class="bg-primary hover:bg-secondary text-white text-sm font-semibold px-4 py-2 rounded">Apply</button>
            <a href="<?= site_url('appointments/manage') ?>" class="text-sm text-gray-600 hover:text-gray-900">Reset filters</a>
        </div>
    </form>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Manage Appointments</h2>
            <div class="flex space-x-2">
                <a href="<?= site_url('appointments') ?>" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded">
                    All Appointments
                </a>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <?php if (empty($appointments)): ?>
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-500">No appointments found.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <?php if (isset($appointment['student_name'])): ?>
                                        <h3 class="font-medium text-gray-900"><?= html_escape($appointment['student_name']) ?></h3>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-700 mt-1"><?= html_escape(date('M d, Y', strtotime($appointment['date']))) ?> at <?= html_escape(date('h:i A', strtotime($appointment['time']))) ?></p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $appointment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($appointment['status'] == 'approved' ? 'bg-green-100 text-green-800' : ($appointment['status'] == 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-3">
                                <?= html_escape(substr($appointment['purpose'] ?? '', 0, 80)) ?>
                                <?php if (strlen($appointment['purpose'] ?? '') > 80): ?>...<?php endif; ?>
                            </p>
                            <div class="mt-4 flex space-x-2">
                                <a href="<?= site_url('appointments/edit/' . $appointment['id']) ?>" class="text-sm bg-primary hover:bg-secondary text-white font-medium py-1 px-3 rounded">Update Status</a>
                                <?php if ($appointment['status'] != 'completed' && $appointment['status'] != 'cancelled'): ?>
                                    <a href="<?= site_url('appointments/delete/' . $appointment['id']) ?>" class="text-sm bg-red-600 hover:bg-red-700 text-white font-medium py-1 px-3 rounded" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>