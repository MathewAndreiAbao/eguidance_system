<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Integration Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .test-card {
            transition: all 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .loading {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">API Integration Test</h1>
            <p class="text-gray-600">Testing all external API integrations for the E-Guidance System</p>
        </div>

        <!-- Test Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            <!-- Calendarific API Test -->
            <div class="test-card bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Calendarific</h2>
                </div>
                <p class="text-gray-600 text-sm mb-4">Holiday data API</p>
                <button onclick="testCalendarific()" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition duration-300">
                    Test API
                </button>
                <div id="calendarific-result" class="mt-4 text-sm"></div>
            </div>

            <!-- Groq AI API Test -->
            <div class="test-card bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Groq AI</h2>
                </div>
                <p class="text-gray-600 text-sm mb-4">AI chatbot API</p>
                <button onclick="testGroq()" class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg transition duration-300">
                    Test API
                </button>
                <div id="groq-result" class="mt-4 text-sm"></div>
            </div>

            <!-- Holiday Check Test -->
            <div class="test-card bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Holiday Check</h2>
                </div>
                <p class="text-gray-600 text-sm mb-4">Holiday validation</p>
                <button onclick="testHolidayCheck()" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg transition duration-300">
                    Test API
                </button>
                <div id="holiday-check-result" class="mt-4 text-sm"></div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Test Results</h2>
            <div id="results-container" class="space-y-4">
                <p class="text-gray-600">Click the buttons above to test each API integration.</p>
            </div>
        </div>
    </div>

    <script>
        function showLoading(elementId) {
            document.getElementById(elementId).innerHTML = `
                <div class="flex items-center text-blue-600">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Testing...
                </div>
            `;
        }

        function showResult(elementId, result) {
            const element = document.getElementById(elementId);
            if (result.status === 'success') {
                element.innerHTML = `<div class="text-green-600 font-medium">${result.message}</div>`;
                if (result.holidays_count !== undefined) {
                    element.innerHTML += `<div class="text-gray-600 mt-1">Holidays found: ${result.holidays_count}</div>`;
                }
                if (result.test_response !== undefined) {
                    element.innerHTML += `<div class="text-gray-600 mt-1">Sample response: ${result.test_response.substring(0, 100)}...</div>`;
                }
            } else {
                element.innerHTML = `<div class="text-red-600 font-medium">${result.message}</div>`;
            }
        }

        function testCalendarific() {
            showLoading('calendarific-result');
            fetch('<?php echo base_url(); ?>api-test/test_calendarific')
                .then(response => response.json())
                .then(data => showResult('calendarific-result', data))
                .catch(error => {
                    document.getElementById('calendarific-result').innerHTML = `<div class="text-red-600">Error: ${error.message}</div>`;
                });
        }

        function testGroq() {
            showLoading('groq-result');
            fetch('<?php echo base_url(); ?>api-test/test_groq')
                .then(response => response.json())
                .then(data => showResult('groq-result', data))
                .catch(error => {
                    document.getElementById('groq-result').innerHTML = `<div class="text-red-600">Error: ${error.message}</div>`;
                });
        }

        function testHolidayCheck() {
            showLoading('holiday-check-result');
            fetch('<?php echo base_url(); ?>api-test/test_holiday_check')
                .then(response => response.json())
                .then(data => showResult('holiday-check-result', data))
                .catch(error => {
                    document.getElementById('holiday-check-result').innerHTML = `<div class="text-red-600">Error: ${error.message}</div>`;
                });
        }
    </script>
</body>
</html>