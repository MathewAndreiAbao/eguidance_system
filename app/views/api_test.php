<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test Dashboard - E-Guidance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .api-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .api-card:hover {
            transform: translateY(-5px);
        }
        .test-result {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
        }
        .success {
            border-left: 4px solid #28a745;
        }
        .error {
            border-left: 4px solid #dc3545;
        }
        .warning {
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <?php include(APP_DIR . 'views/includes/header.php'); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">API Test Dashboard</h1>
                <p class="text-center text-muted">Test and monitor external API integrations</p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card api-card">
                    <div class="card-body">
                        <h5 class="card-title">Calendarific API Test</h5>
                        <p class="card-text">Test holiday data retrieval from Calendarific API</p>
                        <button class="btn btn-primary test-api-btn" data-api="calendarific">Test API</button>
                        <div class="test-result d-none" id="calendarific-result"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card api-card">
                    <div class="card-body">
                        <h5 class="card-title">Groq AI API Test</h5>
                        <p class="card-text">Test AI chatbot functionality with Groq API</p>
                        <button class="btn btn-primary test-api-btn" data-api="groq">Test API</button>
                        <div class="test-result d-none" id="groq-result"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card api-card">
                    <div class="card-body">
                        <h5 class="card-title">ZenQuotes API Test</h5>
                        <p class="card-text">Test inspirational quotes retrieval from ZenQuotes API</p>
                        <button class="btn btn-primary test-api-btn" data-api="zenquotes">Test API</button>
                        <div class="test-result d-none" id="zenquotes-result"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card api-card">
                    <div class="card-body">
                        <h5 class="card-title">Holiday Check Test</h5>
                        <p class="card-text">Test holiday detection functionality</p>
                        <button class="btn btn-primary test-api-btn" data-api="holiday_check">Test API</button>
                        <div class="test-result d-none" id="holiday_check-result"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">API Configuration Status</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>API Service</th>
                                        <th>Status</th>
                                        <th>Configuration</th>
                                        <th>Last Test</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Calendarific</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>API Key: <?php echo substr(config_item('calendarific_api_key'), 0, 10); ?>...</td>
                                        <td>Not tested yet</td>
                                    </tr>
                                    <tr>
                                        <td>Groq AI</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>API Key: <?php echo substr(config_item('groq_api_key'), 0, 10); ?>...</td>
                                        <td>Not tested yet</td>
                                    </tr>
                                    <tr>
                                        <td>ZenQuotes</td>
                                        <td><span class="badge bg-warning">Optional</span></td>
                                        <td>No API Key Required</td>
                                        <td>Not tested yet</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include(APP_DIR . 'views/includes/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.test-api-btn').forEach(button => {
            button.addEventListener('click', function() {
                const api = this.getAttribute('data-api');
                const resultDiv = document.getElementById(api + '-result');
                
                // Show loading state
                resultDiv.classList.remove('d-none', 'success', 'error');
                resultDiv.classList.add('warning');
                resultDiv.innerHTML = '<p>Testing ' + api + ' API... Please wait.</p>';
                resultDiv.classList.remove('d-none');
                
                // Make AJAX request to test the API
                fetch('<?php echo site_url('api-test/test_') ?>' + api)
                    .then(response => response.text())
                    .then(data => {
                        resultDiv.classList.remove('warning');
                        resultDiv.classList.add('success');
                        resultDiv.innerHTML = '<pre>' + data + '</pre>';
                    })
                    .catch(error => {
                        resultDiv.classList.remove('warning');
                        resultDiv.classList.add('error');
                        resultDiv.innerHTML = '<p>Error testing ' + api + ' API:</p><pre>' + error + '</pre>';
                    });
            });
        });
    </script>
</body>
</html>