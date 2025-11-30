<?php require_once __DIR__ . '/../includes/header.php'; ?>

<?php
    $session = Registry::get_object('session');
    if (!$session) {
        $session = load_class('session', 'libraries');
    }
    $error_msg = $session ? $session->flashdata('error') : null;
?>

<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-4 flex justify-between items-center">
        <a href="<?= site_url('wellness-forms') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Wellness Forms
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Create Wellness Form</h2>
            <p class="text-sm text-gray-600 mt-1">Build a short survey to gauge student wellbeing.</p>
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

            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Questions</h3>
                    <button type="button" id="addQuestionBtn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        + Add question
                    </button>
                </div>
                <div id="questionList" class="space-y-4">
                    <!-- Question rows injected via JS -->
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-md shadow">
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
        wrapper.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50';
        wrapper.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1 mr-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                    <input type="text" name="questions[]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your question" required>
                </div>
                <button type="button" class="text-red-600 hover:text-red-800 text-sm remove-question">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Answer type</label>
                    <select name="question_types[]" class="question-type w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="text">Open text</option>
                        <option value="scale">Scale (1-5)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scale minimum</label>
                    <input type="number" name="scale_min[]" class="scale-input w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="1" min="1" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scale maximum</label>
                    <input type="number" name="scale_max[]" class="scale-input w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="5" min="2" readonly>
                </div>
            </div>
        `;

        // Attach listeners
        wrapper.querySelector('.remove-question').addEventListener('click', () => {
            if (document.querySelectorAll('#questionList .border').length > 1) {
                wrapper.remove();
            }
        });

        const toggleScaleFields = () => {
            const type = wrapper.querySelector('.question-type').value;
            const scaleInputs = wrapper.querySelectorAll('.scale-input');
            const scaleParent = scaleInputs.length > 0 ? scaleInputs[0].closest('.grid').parentElement : null;
            
            if (scaleParent) {
                scaleParent.style.display = type === 'scale' ? 'grid' : 'none';
            }
        };

        wrapper.querySelector('.question-type').addEventListener('change', toggleScaleFields);
        toggleScaleFields();

        return wrapper;
    };

    addBtn.addEventListener('click', () => {
        questionList.appendChild(createQuestionRow());
    });

    // Initialize with one question
    const initialRow = createQuestionRow();
    questionList.appendChild(initialRow);
    
    // Hide scale fields by default for initial row
    const initialTypeSelect = initialRow.querySelector('.question-type');
    if (initialTypeSelect && initialTypeSelect.value !== 'scale') {
        const scaleInputs = initialRow.querySelectorAll('.scale-input');
        const scaleParent = scaleInputs.length > 0 ? scaleInputs[0].closest('.grid').parentElement : null;
        if (scaleParent) {
            scaleParent.style.display = 'none';
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

