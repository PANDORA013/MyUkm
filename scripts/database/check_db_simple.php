<?php

// Database configuration
$config = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'myukm_test',
    'port' => 3306
];

// Create connection
try {
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database'], $config['port']);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected successfully to MySQL\n";
    
    // List all tables
    echo "\n=== Tables in database ===\n";
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        echo "- {$row[0]}\n";
    }
    
    // Check if groups table exists
    echo "\n=== Checking groups table ===\n";
    $result = $conn->query("SHOW TABLES LIKE 'groups'");
    
    if ($result->num_rows === 0) {
        die("The 'groups' table does not exist.\n");
    }
    
    // Get columns in groups table
    echo "\n=== Columns in groups table ===\n";
    $result = $conn->query("SHOW COLUMNS FROM `groups`");
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    // Check for indexes on groups table
    echo "\n=== Indexes on groups table ===\n";
    $result = $conn->query("SHOW INDEX FROM `groups`");
    if ($result === false) {
        echo "Error getting indexes: " . $conn->error . "\n";
    } else {
        while ($row = $result->fetch_assoc()) {
            echo "- Index: {$row['Key_name']} on {$row['Column_name']} ";
            echo "({$row['Index_type']}, Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . ")\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
