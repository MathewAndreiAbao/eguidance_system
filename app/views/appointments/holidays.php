<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900">Philippine Holidays</h2>
            <p class="mt-1 text-sm text-gray-600">List of upcoming Philippine holidays from Calendarific API</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <?php if (!empty($holidays) && is_array($holidays)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($holidays as $holiday): ?>
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($holiday['name']) ?></h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <span class="font-medium"><?= date('F j, Y', strtotime($holiday['date']['iso'])) ?></span>
                                        <?php if (!empty($holiday['date']['weekday']['name'])): ?>
                                            (<?= htmlspecialchars($holiday['date']['weekday']['name']) ?>)
                                        <?php endif; ?>
                                    </p>
                                    <?php if (!empty($holiday['description'])): ?>
                                        <p class="text-sm text-gray-600 mt-2"><?= htmlspecialchars($holiday['description']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($holiday['type']) && is_array($holiday['type'])): ?>
                                        <div class="mt-2">
                                            <?php foreach ($holiday['type'] as $type): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <?= htmlspecialchars(ucfirst($type)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No holidays found</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no holidays available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>