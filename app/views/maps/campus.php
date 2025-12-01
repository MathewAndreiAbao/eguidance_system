<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Map - E-Guidance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .legend {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .legend h6 {
            margin: 0 0 5px;
            font-size: 14px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 8px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <?php include(APP_DIR . 'views/includes/header.php'); ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Interactive Campus Map</h1>
                <p class="text-center text-muted">Explore our campus facilities and buildings</p>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div id="map"></div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Map Controls</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" id="zoomIn">Zoom In</button>
                            <button type="button" class="btn btn-outline-primary" id="zoomOut">Zoom Out</button>
                            <button type="button" class="btn btn-outline-secondary" id="resetView">Reset View</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Location Search</h5>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search locations..." id="locationSearch">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Campus Facilities</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="legend-color bg-primary"></div>
                                    <span>Administration</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="legend-color bg-success"></div>
                                    <span>Academic Buildings</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="legend-color bg-warning"></div>
                                    <span>Student Services</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="legend-color bg-danger"></div>
                                    <span>Recreation</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include(APP_DIR . 'views/includes/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([13.4108, 121.1797], 16);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add markers for campus locations
        var locations = [
            {
                name: "Administration Building",
                lat: 13.4108,
                lng: 121.1797,
                type: "administration",
                description: "Main administrative offices and student services"
            },
            {
                name: "Library",
                lat: 13.4105,
                lng: 121.1795,
                type: "academic",
                description: "Main library with study areas and resources"
            },
            {
                name: "Cafeteria",
                lat: 13.4110,
                lng: 121.1799,
                type: "services",
                description: "Student cafeteria and dining area"
            },
            {
                name: "Student Lounge",
                lat: 13.4107,
                lng: 121.1800,
                type: "recreation",
                description: "Relaxation and social area for students"
            }
        ];
        
        // Add markers to the map
        locations.forEach(function(location) {
            var marker = L.marker([location.lat, location.lng]).addTo(map);
            marker.bindPopup(
                "<b>" + location.name + "</b><br>" + 
                location.description
            );
        });
        
        // Map controls
        document.getElementById('zoomIn').addEventListener('click', function() {
            map.zoomIn();
        });
        
        document.getElementById('zoomOut').addEventListener('click', function() {
            map.zoomOut();
        });
        
        document.getElementById('resetView').addEventListener('click', function() {
            map.setView([13.4108, 121.1797], 16);
        });
        
        // Search functionality
        document.getElementById('searchBtn').addEventListener('click', function() {
            var searchTerm = document.getElementById('locationSearch').value.toLowerCase();
            locations.forEach(function(location) {
                if (location.name.toLowerCase().includes(searchTerm)) {
                    map.setView([location.lat, location.lng], 18);
                    // In a real implementation, you would highlight the marker
                }
            });
        });
    </script>
</body>
</html>