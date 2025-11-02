<?php require_once __DIR__ . '/includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php $success_msg = $session->flashdata('success'); if (!empty($success_msg)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <?php $error_msg = $session->flashdata('error'); if (!empty($error_msg)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Appointments</h2>
            <?php if($session->userdata('role') == 'student'): ?>
                <a href="<?= site_url('appointments/create') ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Book Appointment
                </a>
            <?php endif; ?>
        </div>
        
        <?php if(empty($appointments)): ?>
            <div class="px-4 py-5 sm:p-6">
                <p class="text-gray-600">No appointments found.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <?php if($session->userdata('role') == 'counselor'): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <?php if($session->userdata('role') == 'counselor'): ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $appointment['student_name'] ?>
                                    </td>
                                <?php endif; ?>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $appointment['date'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $appointment['time'] ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?= $appointment['purpose'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $appointment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($appointment['status'] == 'approved' ? 'bg-green-100 text-green-800' : 
                                                ($appointment['status'] == 'completed' ? 'bg-blue-100 text-blue-800' : 
                                                'bg-red-100 text-red-800')) ?>">
                                        <?= ucfirst($appointment['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if($session->userdata('role') == 'student' && $appointment['status'] == 'pending'): ?>
                                        <a href="<?= site_url('appointments/edit/' . $appointment['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <a href="<?= site_url('appointments/delete/' . $appointment['id']) ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
                                    <?php elseif($session->userdata('role') == 'counselor'): ?>
                                        <a href="<?= site_url('appointments/edit/' . $appointment['id']) ?>" class="text-blue-600 hover:text-blue-900">Update Status</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>