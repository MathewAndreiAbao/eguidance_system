<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $success_msg = $session ? $session->flashdata('success') : null;
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php if (!empty($success_msg)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900">Upcoming Appointments</h2>
        </div>
        <div class="border-t border-gray-200">
            <?php if (empty($appointments)): ?>
                <div class="px-6 py-8 text-center">
                    <p class="text-gray-500">No upcoming appointments.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900"><?= html_escape($appointment['counselor_name'] ?? 'N/A') ?></h3>
                                    <p class="text-sm text-gray-500 mt-1"><?= html_escape(date('M d, Y', strtotime($appointment['date']))) ?> at <?= html_escape(date('h:i A', strtotime($appointment['time']))) ?></p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $appointment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($appointment['status'] == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (isset($pagination)): ?>
        <div class="mt-6 flex justify-center">
            <?= $pagination ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>