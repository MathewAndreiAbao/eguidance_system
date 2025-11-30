<?php 
$current_page = 'maps';
// Use map configuration passed from controller
if (!isset($map_config)) {
    $map_config = require_once APP_DIR . 'config/maps.php';
}
require_once __DIR__ . '/../includes/header.php'; 
?>

<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Guidance and Counseling Office Map</h1>
                <p class="mt-2 text-sm text-gray-600">Interactive map of Guidance and Counseling Office, Mindoro State University Calapan Campus</p>
            </div>
        </div>
    </div>

    <!-- Map Integration Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">OpenStreetMap API Integration</h3>
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle"></i> Interactive campus map
            </div>
        </div>
        
        <!-- Map Container -->
        <div id="campus-map" class="w-full h-96 rounded-lg border border-gray-200 mb-6"></div>
        
        <!-- Map Integration Details -->
        <div class="prose max-w-none">
            <h4 class="text-md font-medium text-gray-800 mb-2">OpenStreetMap API Setup</h4>
            <p class="text-sm text-gray-600 mb-3">Interactive campus map implementation details:</p>
            
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h5 class="font-medium text-gray-700 mb-2">Implementation Details:</h5>
                <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600">
                    <li><strong>Library Used:</strong> Leaflet.js (v1.9.4)</li>
                    <li><strong>Tile Provider:</strong> OpenStreetMap</li>
                    <li><strong>Endpoint:</strong> https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png</li>
                    <li><strong>Attribution:</strong> &copy; <a href="https://www.openstreetmap.org/copyright" class="text-primary hover:underline">OpenStreetMap</a> contributors</li>
                    <li><strong>Default Coordinates:</strong> Latitude 13.388380, Longitude 121.162682 (Guidance and Counseling Office, Mindoro State University Calapan Campus)</li>
                    <li><strong>Zoom Level:</strong> 19</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <h5 class="font-medium text-gray-700 mb-2">Configuration:</h5>
                <p class="text-sm text-gray-600 mb-2">The OpenStreetMap integration is implemented directly in the frontend JavaScript without requiring API keys or server-side credentials.</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Implementation can be found in:</strong></p>
                <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600">
                    <li>File: app/views/maps/campus.php</li>
                    <li>Controller: app/controllers/MapsController.php</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <h5 class="font-medium text-gray-700 mb-2">Features:</h5>
                <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600">
                    <li>Interactive campus map centered on Guidance and Counseling Office</li>
                    <li>Marker for Guidance and Counseling Office (coordinates: 13.388380, 121.162682)</li>
                    <li>Scale control for distance measurement</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    var map = L.map('campus-map').setView([<?php echo $map_config['default_lat']; ?>, <?php echo $map_config['default_lng']; ?>], <?php echo $map_config['default_zoom']; ?>);
    
    // Add OpenStreetMap tiles
    L.tileLayer('<?php echo $map_config['tile_provider']; ?>', {
        attribution: '<?php echo $map_config['attribution']; ?>'
    }).addTo(map);
    
    // Add marker for Guidance and Counseling Office
    var counselingOffice = L.marker([<?php echo $map_config['default_lat']; ?>, <?php echo $map_config['default_lng']; ?>]).addTo(map)
        .bindPopup('Guidance and Counseling Office<br>Mindoro State University')
        .openPopup();
    
    // Add scale control
    L.control.scale({imperial: false}).addTo(map);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>