<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    
    $error_msg = $session->flashdata('error');
    
    // Use holidays data from controller, or empty array as fallback
    $holidays = isset($holidays) ? $holidays : [];
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <?php if (!empty($error_msg)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900">Book Appointment</h2>
            <p class="mt-1 text-sm text-gray-600">Holidays are hidden from the calendar. <a href="<?= site_url('appointments/holidays') ?>" class="text-blue-600 hover:text-blue-800">View all holidays</a></p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form action="<?= site_url('appointments/create') ?>" method="POST">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <div class="relative">
                            <input type="text" id="date-display" readonly
                                   class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md cursor-pointer bg-white"
                                   placeholder="Select Date">
                            <input type="hidden" name="date" id="date" required>
                            
                            <!-- Calendar Picker -->
                            <div id="date-picker" class="absolute z-10 mt-1 bg-white shadow-lg rounded-md p-4 hidden">
                                <div class="flex items-center justify-between mb-2">
                                    <button type="button" id="prev-month" class="p-1 rounded hover:bg-gray-100">&lt;</button>
                                    <div id="current-month-year" class="font-medium"></div>
                                    <button type="button" id="next-month" class="p-1 rounded hover:bg-gray-100">&gt;</button>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-gray-500 mb-1">
                                    <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                                </div>
                                <div id="calendar-days" class="grid grid-cols-7 gap-1"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                        <select name="time" id="time" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" required>
                            <option value="">Select Time</option>
                            <!-- Time options will be populated dynamically by JavaScript -->
                        </select>
                        <p id="time-availability" class="mt-1 text-sm text-gray-600"></p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="counselor_id" class="block text-sm font-medium text-gray-700">Preferred Counselor</label>
                        <select name="counselor_id" id="counselor_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="">Select Counselor (Optional)</option>
                            <?php if (!empty($counselors) && is_array($counselors)): ?>
                                <?php foreach($counselors as $counselor): ?>
                                    <?php $label = html_escape($counselor['name'] ?? ($counselor['username'] ?? "Counselor #{$counselor['id']}")); ?>
                                    <option value="<?= html_escape($counselor['id']) ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No counselors available</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="purpose" class="block text-sm font-medium text-gray-700">Purpose of Appointment</label>
                        <textarea name="purpose" id="purpose" rows="4" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required></textarea>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" id="submit-btn" class="bg-primary hover:bg-secondary text-white font-bold py-2 px-4 rounded mr-2">Book Appointment</button>
                    <a href="<?= site_url('appointments') ?>" class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Holidays data from PHP
const holidays = <?= json_encode($holidays) ?>;

let bookedSlots = {}; // Cache for booked time slots
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

// Function to check if a date is a holiday
function isHoliday(dateString) {
    return holidays.includes(dateString);
}

// Function to format date as YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Function to render calendar
function renderCalendar() {
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const endDate = new Date(lastDay);
    endDate.setDate(endDate.getDate() + (6 - lastDay.getDay()));
    
    document.getElementById('current-month-year').textContent = 
        firstDay.toLocaleString('default', { month: 'long', year: 'numeric' });
    
    // Generate all days in the calendar grid
    const calendarDays = document.getElementById('calendar-days');
    calendarDays.innerHTML = '';
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Create a working copy of the start date
    const workingDate = new Date(startDate);
    
    while (workingDate <= endDate) {
        const currentDate = new Date(workingDate); // Create a copy for this iteration
        const dateStr = formatDate(currentDate);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'text-center p-1 text-sm rounded cursor-pointer';
        dayElement.textContent = currentDate.getDate();
        
        // Check if date is in current month
        if (currentDate.getMonth() !== currentMonth) {
            dayElement.classList.add('text-gray-300');
        } else {
            // Check if date is today or in the past
            if (currentDate < today) {
                dayElement.classList.add('text-gray-300', 'cursor-not-allowed');
            } 
            // Check if date is a holiday
            else if (isHoliday(dateStr)) {
                dayElement.classList.add('hidden'); // Hide holiday dates completely
            }
            // Available date
            else {
                dayElement.classList.add('hover:bg-blue-100');
                dayElement.addEventListener('click', () => selectDate(currentDate));
            }
        }
        
        calendarDays.appendChild(dayElement);
        
        // Move to next day
        workingDate.setDate(workingDate.getDate() + 1);
    }
}

// Function to select date
function selectDate(date) {
    const dateStr = formatDate(date);
    document.getElementById('date').value = dateStr;
    document.getElementById('date-display').value = date.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    document.getElementById('date-picker').classList.add('hidden');
    updateAvailableTimes();
}

// Event listeners for calendar navigation
document.getElementById('prev-month').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar();
});

document.getElementById('next-month').addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar();
});

// Show/hide calendar picker
document.getElementById('date-display').addEventListener('click', () => {
    document.getElementById('date-picker').classList.toggle('hidden');
});

// Close calendar when clicking outside
document.addEventListener('click', (e) => {
    const datePicker = document.getElementById('date-picker');
    const dateDisplay = document.getElementById('date-display');
    if (!datePicker.classList.contains('hidden') && 
        !datePicker.contains(e.target) && 
        e.target !== dateDisplay) {
        datePicker.classList.add('hidden');
    }
});

// Initialize calendar
renderCalendar();

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
    
    // Show loading message
    const availabilityText = document.getElementById('time-availability');
    availabilityText.textContent = 'Loading available time slots...';
    availabilityText.className = 'mt-1 text-sm text-blue-600';
    
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
                timeSelect.appendChild(option);
            }
        }
        
        // Update availability message
        const availableCount = timeSelect.options.length - 1; // Subtract 1 for the "Select Time" option
        if (availableCount > 0) {
            availabilityText.textContent = `${availableCount} time slot${availableCount !== 1 ? 's' : ''} available for this date.`;
            availabilityText.className = 'mt-1 text-sm text-green-600';
        } else {
            availabilityText.textContent = 'No time slots available for this date. Please select another date.';
            availabilityText.className = 'mt-1 text-sm text-red-600';
        }
    } catch (error) {
        console.error('Error updating time slots:', error);
        availabilityText.textContent = 'Error loading time slots. Please try again.';
        availabilityText.className = 'mt-1 text-sm text-red-600';
    }
}

// Event listener for counselor selection change
document.getElementById('counselor_id').addEventListener('change', () => {
    const selectedDate = document.getElementById('date').value;
    if (selectedDate) {
        updateAvailableTimes();
    }
});

// Initialize time availability check
document.getElementById('time').addEventListener('change', () => {
    const selectedDate = document.getElementById('date').value;
    const selectedTime = document.getElementById('time').value;
    const availabilityText = document.getElementById('time-availability');
    
    if (!selectedDate || !selectedTime) {
        availabilityText.textContent = '';
        return;
    }
    
    availabilityText.textContent = 'This time slot is available.';
    availabilityText.className = 'mt-1 text-sm text-green-600';
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>