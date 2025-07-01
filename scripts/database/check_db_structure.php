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
    
    echo "=== Database Connection Successful ===\n\n";
    
    // Get database version
    $result = $conn->query("SELECT VERSION() as version");
    $row = $result->fetch_assoc();
    echo "MySQL Version: " . $row['version'] . "\n\n";
    
    // List all tables
    echo "=== Database Tables ===\n";
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
        echo "- {$row[0]}\n";
    }
    
    // Check groups table structure
    if (in_array('groups', $tables)) {
        echo "\n=== Groups Table Structure ===\n";
        
        // Get columns
        $result = $conn->query("SHOW COLUMNS FROM `groups`");
        echo "\nColumns:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})\n";
        }
        
        // Get indexes
        $result = $conn->query("SHOW INDEX FROM `groups`");
        if ($result->num_rows > 0) {
            echo "\nIndexes:\n";
            while ($row = $result->fetch_assoc()) {
                echo "- {$row['Key_name']} on {$row['Column_name']} ";
                echo "({$row['Index_type']}, Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . ")\n";
            }
        } else {
            echo "\nNo indexes found on groups table\n";
        }
        
        // Check for any data
        $result = $conn->query("SELECT COUNT(*) as count FROM `groups`");
        $row = $result->fetch_assoc();
        echo "\nNumber of records in groups table: " . $row['count'] . "\n";
    } else {
        echo "\nThe 'groups' table does not exist.\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
