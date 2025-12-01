<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Maps - E-Guidance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-container {
            height: 500px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .location-card {
            transition: transform 0.2s;
        }
        .location-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include(APP_DIR . 'views/includes/header.php'); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Campus Maps</h1>
                <p class="text-center text-muted">Explore our campus with interactive maps and location guides.</p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4 mb-4">
                <div class="card location-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Interactive Campus Map</h5>
                        <p class="card-text">View our complete campus with building locations, facilities, and navigation aids.</p>
                        <a href="<?php echo site_url('maps/campus'); ?>" class="btn btn-primary">View Map</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card location-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Building Directory</h5>
                        <p class="card-text">Find specific buildings, departments, and offices with our searchable directory.</p>
                        <a href="#" class="btn btn-secondary disabled">Coming Soon</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card location-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Emergency Contacts</h5>
                        <p class="card-text">Quick access to emergency services and important campus contact numbers.</p>
                        <a href="#" class="btn btn-secondary disabled">Coming Soon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include(APP_DIR . 'views/includes/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>