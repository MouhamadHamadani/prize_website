<?php
// Database Setup Script for Prize Website
// Run this script once to initialize the database

require_once 'includes/config.php';

echo "<h1>Prize Website Database Setup</h1>";

try {
    // Connect to MySQL without database selection
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Connected to MySQL server</p>";
    
    // Read and execute SQL schema
    $sql = file_get_contents('database/schema.sql');
    
    if ($sql === false) {
        throw new Exception("Could not read schema.sql file");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "<p>✓ Database schema created successfully</p>";
    echo "<p>✓ Sample data inserted</p>";
    echo "<p>✓ Default admin user created (username: admin, password: admin123)</p>";
    
    // Test database connection with the new database
    $testDb = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $testDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $prizeCount = $testDb->query("SELECT COUNT(*) FROM prizes")->fetchColumn();
    echo "<p>✓ Database connection test passed - Found {$prizeCount} sample prizes</p>";
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p>Your Prize Website is now ready to use:</p>";
    echo "<ul>";
    echo "<li><strong>Website:</strong> <a href='/'>Homepage</a></li>";
    echo "<li><strong>Admin Panel:</strong> <a href='/admin/login.php'>Admin Login</a> (admin / admin123)</li>";
    echo "<li><strong>User Registration:</strong> <a href='/auth/register.php'>Sign Up</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Important:</strong> Delete this setup.php file after setup is complete for security.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in includes/config.php</p>";
}
?>

