<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EGuidance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-blue-600">EGuidance System</h1>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="<?= site_url('profile') ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Profile</a>
                    <a href="<?= site_url('auth/logout') ?>" class="ml-4 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php
            // Resolve session via Registry so views don't rely on controller internals
            $session = Registry::get_object('session');
            if (!$session) {
                $session = load_class('session', 'libraries');
            }
            $success_msg = $session ? $session->flashdata('success') : null;
            $role = $session ? $session->userdata('role') : null;
        ?>

        <?php if (!empty($success_msg)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= html_escape($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if($role == 'student'): ?>
            <!-- Student Dashboard -->
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900">Welcome, <?= html_escape($profile['name']) ?></h2>
                        <p class="mt-1 text-sm text-gray-600">Student Dashboard</p>
                        
                        <div class="mt-4">
                            <a href="<?= site_url('appointments/create') ?>" 
                               class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                Book Appointment
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">My Appointments</h3>
                        <?php if(empty($appointments)): ?>
                            <p class="text-gray-600">No appointments found.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Counselor</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach($appointments as $appointment): ?>
                                            <?php
                                                $counselor_label = $appointment['counselor_name'] ?? $appointment['counselor'] ?? $appointment['counselor_username'] ?? (isset($appointment['counselor_id']) ? "Counselor #{$appointment['counselor_id']}" : 'N/A');
                                                $appt_date = $appointment['date'] ?? '';
                                                $appt_time = $appointment['time'] ?? '';
                                                $appt_status = $appointment['status'] ?? '';
                                            ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($appt_date) ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($appt_time) ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($counselor_label) ?></td>
                                                <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($appointment['purpose'] ?? '') ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $appt_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($appt_status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                                        <?= htmlspecialchars(ucfirst($appt_status)) ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <?php if ($appt_status == 'pending'): ?>
                                                        <a href="<?= site_url('appointments/edit/' . $appointment['id']) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                                        <a href="<?= site_url('appointments/delete/' . $appointment['id']) ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
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
            </div>
        <?php else: ?>
            <!-- Counselor Dashboard -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">All Appointments</h2>
                <?php if(empty($all_appointments)): ?>
                    <p class="text-gray-600">No appointments found.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach($all_appointments as $appointment): ?>
                                    <?php
                                        $student_label_all = $appointment['student_name'] ?? $appointment['student'] ?? $appointment['student_username'] ?? (isset($appointment['student_id']) ? "Student #{$appointment['student_id']}" : 'N/A');
                                        $a_date = $appointment['date'] ?? '';
                                        $a_time = $appointment['time'] ?? '';
                                        $a_status = $appointment['status'] ?? '';
                                    ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($student_label_all) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($a_date) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($a_time) ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($appointment['purpose'] ?? '') ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $a_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($a_status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') ?>">
                                                <?= htmlspecialchars(ucfirst($a_status)) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="<?= site_url('appointments/edit/' . ($appointment['id'] ?? '')) ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>