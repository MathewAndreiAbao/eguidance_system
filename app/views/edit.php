<?php require_once __DIR__ . '/includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900">
                <?= $session->userdata('role') == 'counselor' ? 'Update Appointment Status' : 'Edit Appointment' ?>
            </h2>
        </div>

        <?php $error_msg = $session->flashdata('error'); if (!empty($error_msg)): ?>
            <div class="px-4 sm:px-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $error_msg ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="px-4 py-5 sm:p-6">
            <form action="<?= site_url('appointments/edit/' . $appointment['id']) ?>" method="POST">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <?php if($session->userdata('role') == 'student'): ?>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" id="date" value="<?= $appointment['date'] ?>" min="<?= date('Y-m-d') ?>"
                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
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
                                <?php foreach($counselors as $counselor): ?>
                                    <option value="<?= $counselor['id'] ?>"<?= $appointment['counselor_id'] == $counselor['id'] ? ' selected' : '' ?>>
                                        <?= $counselor['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose of Appointment</label>
                            <textarea name="purpose" id="purpose" rows="4" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required><?= $appointment['purpose'] ?></textarea>
                        </div>

                    <?php else: ?>
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
                                <p><strong>Student:</strong> <?= $appointment['student_name'] ?></p>
                                <p><strong>Date:</strong> <?= $appointment['date'] ?></p>
                                <p><strong>Time:</strong> <?= date('h:i A', strtotime($appointment['time'])) ?></p>
                                <p><strong>Purpose:</strong> <?= $appointment['purpose'] ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        <?= $session->userdata('role') == 'counselor' ? 'Update Status' : 'Save Changes' ?>
                    </button>
                    <a href="<?= site_url('appointments') ?>" class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>