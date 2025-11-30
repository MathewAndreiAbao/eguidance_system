<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $error_msg = $session ? $session->flashdata('error') : null;
?>

<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="<?= site_url('wellness-forms') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Wellness Forms
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Create New Wellness Form</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Create Wellness Form</h2>
            <p class="text-base text-gray-600 mt-2 max-w-2xl mx-auto">Build a short survey to gauge student wellbeing. Add questions and configure answer types to create an effective wellness assessment.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= html_escape($error_msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('wellness-forms/create') ?>" id="wellnessFormBuilder" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Weekly Wellbeing Check-in" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Explain what the form is about (optional)"></textarea>
            </div>

            <div class="flex items-center">
                <input id="is_active" name="is_active" type="checkbox" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded" checked>
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Keep form open for student responses</label>
            </div>

            <div class="pt-4">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                    <button type="button" id="addQuestionBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Question
                    </button>
                </div>
                <div id="questionList" class="space-y-4">
                    <!-- Question rows injected via JS -->
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Wellness Form
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const questionList = document.getElementById('questionList');
    const addBtn = document.getElementById('addQuestionBtn');

    const createQuestionRow = () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50 mb-4';
        wrapper.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1 mr-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                    <input type="text" name="questions[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your question" required>
                </div>
                <button type="button" class="text-red-600 hover:text-red-800 text-sm remove-question font-medium">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Answer type</label>
                    <select name="question_types[]" class="question-type w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="text">Open text</option>
                        <option value="scale">Scale (1-5)</option>
                    </select>
                </div>
                <div class="scale-fields" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scale minimum</label>
                    <input type="number" name="scale_min[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="1" min="1">
                </div>
                <div class="scale-fields" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scale maximum</label>
                    <input type="number" name="scale_max[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="5" min="2">
                </div>
            </div>
        `;

        // Attach listeners after the element is added to the DOM
        setTimeout(() => {
            const removeBtn = wrapper.querySelector('.remove-question');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    if (document.querySelectorAll('#questionList .border').length > 1) {
                        wrapper.remove();
                    } else {
                        // Show error message if trying to remove the last question
                        alert('At least one question is required.');
                    }
                });
            }

            const typeSelect = wrapper.querySelector('.question-type');
            const scaleFields = wrapper.querySelectorAll('.scale-fields');
            
            if (typeSelect && scaleFields.length > 0) {
                const toggleScaleFields = () => {
                    const type = typeSelect.value;
                    scaleFields.forEach(field => {
                        field.style.display = type === 'scale' ? 'block' : 'none';
                    });
                };

                typeSelect.addEventListener('change', toggleScaleFields);
                // Initialize the display based on the default value
                toggleScaleFields();
            }
        }, 0);

        return wrapper;
    };

    // Initialize with one question
    const initialRow = createQuestionRow();
    questionList.appendChild(initialRow);
    
    // Add event listener for the "Add question" button
    addBtn.addEventListener('click', () => {
        const newRow = createQuestionRow();
        questionList.appendChild(newRow);
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

