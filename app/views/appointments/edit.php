<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $error_msg = $session ? $session->flashdata('error') : null;
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
                            <input type="date" name="date" id="date" value="<?= $appointment['date'] ?>" min="<?= date('Y-m-d') ?>" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                            <select name="time" id="time" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" required>
                                <option value="">Select Time</option>
                                <!-- Time options will be populated dynamically by JavaScript -->
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="counselor_id" class="block text-sm font-medium text-gray-700">Preferred Counselor</label>
                            <select name="counselor_id" id="counselor_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
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
                            <?php $purpose_value = $appointment['purpose'] ?? ''; ?>
                            <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose of Appointment</label>
                            <textarea name="purpose" id="purpose" rows="4" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required><?= html_escape($purpose_value) ?></textarea>
                        </div>

                    <?php elseif($session && $session->userdata('role') == 'counselor'): ?>
                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" required>
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
                    <button type="submit" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded mr-2"><?php echo ($session && $session->userdata('role') == 'counselor') ? 'Update Status' : 'Save Changes'; ?></button>
                    <a href="<?= site_url('appointments') ?>" class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Function to get available time slots for a date
async function getAvailableTimes(date, counselorId = null) {
    const formData = new FormData();
    formData.append('date', date);
    if (counselorId) {
        formData.append('counselor_id', counselorId);
    }
    
    try {
        const response = await fetch('<?= site_url('appointments/check_availability') ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        return data.booked_slots || [];
    } catch (error) {
        console.error('Error fetching booked slots:', error);
        return [];
    }
}

// Function to populate time dropdown with only available slots
async function updateAvailableTimes() {
    const selectedDate = document.getElementById('date').value;
    const timeSelect = document.getElementById('time');
    const counselorSelect = document.getElementById('counselor_id');
    const counselorId = counselorSelect ? counselorSelect.value : null;
    
    // Clear current options except the first one
    timeSelect.innerHTML = '<option value="">Select Time</option>';
    
    if (!selectedDate) return;
    
    try {
        // Get booked slots for the selected date
        const bookedSlots = await getAvailableTimes(selectedDate, counselorId);
        
        // Generate time slots from 8:00 AM to 5:00 PM (hourly)
        const startHour = 8;
        const endHour = 17;
        
        for (let hour = startHour; hour <= endHour; hour++) {
            const timeValue = `${hour.toString().padStart(2, '0')}:00:00`;
            const timeDisplay = `${(hour % 12 || 12)}:${'00'.padStart(2, '0')} ${hour >= 12 ? 'PM' : 'AM'}`;
            
            // Only add time slot if it's not booked
            if (!bookedSlots.includes(timeValue)) {
                const option = document.createElement('option');
                option.value = timeValue;
                option.textContent = timeDisplay;
                
                // Check if this is the currently selected time
                const currentAppointmentTime = '<?= $appointment['time'] ?? '' ?>';
                if (currentAppointmentTime === timeValue) {
                    option.selected = true;
                }
                
                timeSelect.appendChild(option);
            }
        }
        
        // If the current appointment time is booked, add it as a disabled option
        const currentAppointmentTime = '<?= $appointment['time'] ?? '' ?>';
        if (currentAppointmentTime && bookedSlots.includes(currentAppointmentTime)) {
            const option = document.createElement('option');
            option.value = currentAppointmentTime;
            option.textContent = `Current Time: ${formatTimeDisplay(currentAppointmentTime)} (Booked)`;
            option.disabled = true;
            option.selected = true;
            timeSelect.appendChild(option);
        }
    } catch (error) {
        console.error('Error updating time slots:', error);
    }
}

// Helper function to format time display
function formatTimeDisplay(timeValue) {
    const [hours, minutes] = timeValue.split(':');
    const hour = parseInt(hours);
    return `${(hour % 12 || 12)}:${minutes} ${hour >= 12 ? 'PM' : 'AM'}`;
}

// Event listener for date change
document.getElementById('date').addEventListener('change', updateAvailableTimes);

// Event listener for counselor selection change
document.getElementById('counselor_id').addEventListener('change', updateAvailableTimes);

// Initialize available times on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set the initial date value
    const dateInput = document.getElementById('date');
    if (dateInput.value) {
        // Small delay to ensure DOM is fully loaded
        setTimeout(updateAvailableTimes, 100);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>