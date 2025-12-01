<?php
/**
 * Database Connection Test Script
 * This script tests the database connection and provides detailed error information
 */

// Prevent direct access
define('PREVENT_DIRECT_ACCESS', true);

// Include necessary files
require_once 'scheme/kernel/Registry.php';
require_once 'scheme/kernel/Config.php';
require_once 'app/config/database.php';

echo "<h1>Database Connection Test</h1>\n";

// Get database configuration
$config = database_config()['main'];
echo "<h2>Configuration:</h2>\n";
echo "<pre>";
print_r($config);
echo "</pre>\n";

// Test connection
try {
    echo "<h2>Testing Connection...</h2>\n";
    
    $driver = strtolower($config['driver']);
    $charset = $config['charset'];
    $host = $config['hostname'];
    $port = $config['port'];
    $dbname = $config['database'];
    $username = $config['username'];
    $password = $config['password'];
    
    echo "<p>Driver: $driver</p>\n";
    echo "<p>Host: $host</p>\n";
    echo "<p>Port: $port</p>\n";
    echo "<p>Database: $dbname</p>\n";
    echo "<p>Username: $username</p>\n";
    
    switch ($driver) {
        case 'mysql':
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset;port=$port";
            break;
        case 'pgsql':
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";
            break;
        case 'sqlite':
            if (empty($path)) {
                throw new Exception('SQLite requires a valid file path.');
            }
            $dsn = "sqlite:$path";
            break;
        case 'sqlsrv':
            $dsn = "sqlsrv:Server=$host,$port;Database=$dbname";
            break;
        default:
            throw new Exception("Unsupported database driver: $driver");
    }
    
    echo "<p>DSN: $dsn</p>\n";
    
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    );
    
    echo "<p>Attempting connection...</p>\n";
    
    $pdo = new PDO($dsn, $username, $password, $options);
    $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    echo "<p style='color: green;'><strong>✓ Connection successful!</strong></p>\n";
    echo "<p>Driver connected: $driverName</p>\n";
    
    // Test a simple query
    try {
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "<p style='color: green;'><strong>✓ Simple query test passed!</strong></p>\n";
        echo "<p>Query result: " . print_r($result, true) . "</p>\n";
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>✗ Query test failed:</strong> " . $e->getMessage() . "</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>✗ Connection failed:</strong> " . $e->getMessage() . "</p>\n";
    echo "<h3>Error Details:</h3>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "<hr>\n";
echo "<h2>Possible Solutions:</h2>\n";
echo "<ul>\n";
echo "<li>Verify database credentials in app/config/database.php</li>\n";
echo "<li>Check if the database server is online and accessible</li>\n";
echo "<li>Verify firewall settings allow connections on port $port</li>\n";
echo "<li>Try connecting with a database client (e.g., MySQL Workbench, phpMyAdmin)</li>\n";
echo "<li>Consider switching to a more reliable database hosting service</li>\n";
echo "</ul>\n";
?>