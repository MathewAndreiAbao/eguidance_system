<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $error_msg = $session ? $session->flashdata('error') : null;
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php
    $dashboard_url = ($session && $session->userdata('role') == 'counselor') ? site_url('counselor/dashboard') : site_url('student/dashboard');
    ?>
    <div class="mb-4">
        <a href="<?= $dashboard_url ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900"><?php echo ($session && $session->userdata('role') == 'counselor') ? 'Update Appointment Status' : 'Edit Appointment'; ?></h2>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <form action="<?= site_url('appointments/edit/' . $appointment['id']) ?>" method="POST">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <?php if($session && $session->userdata('role') == 'student'): ?>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" value="<?= $appointment['date'] ?>" min="<?= date('Y-m-d') ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                            <select name="time" id="time" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <?php
                                $start = strtotime('08:00');
                                $end = strtotime('17:00');
                                for ($i = $start; $i <= $end; $i += 3600) {
                                    $time = date('H:i:s', $i);
                                    echo '<option value="' . $time . '"' . ($appointment['time'] == $time ? ' selected' : '') . '>' . date('h:i A', $i) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="counselor_id" class="block text-sm font-medium text-gray-700">Preferred Counselor</label>
                            <select name="counselor_id" id="counselor_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Counselor (Optional)</option>
                                <?php if (!empty($counselors) && is_array($counselors)): ?>
                                <?php foreach($counselors as $counselor): ?>
                                    <?php $label = html_escape($counselor['name'] ?? ($counselor['username'] ?? "Counselor #{$counselor['id']}")); ?>
                                    <option value="<?= html_escape($counselor['id']) ?>"<?= $appointment['counselor_id'] == $counselor['id'] ? ' selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No counselors available</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose of Appointment</label>
                            <textarea name="purpose" id="purpose" rows="4" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required><?= $appointment['purpose'] ?></textarea>
                        </div>

                    <?php elseif($session && $session->userdata('role') == 'counselor'): ?>
                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="pending"<?= $appointment['status'] == 'pending' ? ' selected' : '' ?>>Pending</option>
                                <option value="approved"<?= $appointment['status'] == 'approved' ? ' selected' : '' ?>>Approved</option>
                                <option value="completed"<?= $appointment['status'] == 'completed' ? ' selected' : '' ?>>Completed</option>
                                <option value="cancelled"<?= $appointment['status'] == 'cancelled' ? ' selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Appointment Details</label>
                            <div class="mt-2 space-y-2">
                                        <?php
                                            $student_label = $appointment['student_name'] ?? $appointment['student'] ?? $appointment['student_username'] ?? (isset($appointment['student_id']) ? "Student #{$appointment['student_id']}" : 'N/A');
                                        ?>
                                        <p><strong>Student:</strong> <?= htmlspecialchars($student_label) ?></p>
                                        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['date'] ?? '') ?></p>
                                        <p><strong>Time:</strong> <?= htmlspecialchars(date('h:i A', strtotime($appointment['time'] ?? '00:00:00'))) ?></p>
                                        <p><strong>Purpose:</strong> <?= htmlspecialchars($appointment['purpose'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2"><?php echo ($session && $session->userdata('role') == 'counselor') ? 'Update Status' : 'Save Changes'; ?></button>
                    <a href="<?= site_url('appointments') ?>" class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>