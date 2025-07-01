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
    
    // Set charset to utf8mb4
    $conn->set_charset("utf8mb4");
    
    echo "=== Database Connection Successful ===\n\n";
    
    // Check if groups table exists
    $result = $conn->query("SHOW TABLES LIKE 'groups'");
    if ($result->num_rows === 0) {
        die("The 'groups' table does not exist.\n");
    }
    
    // Get table structure
    echo "=== Groups Table Structure ===\n";
    
    // Get columns using information_schema
    $query = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA 
              FROM information_schema.COLUMNS 
              WHERE TABLE_SCHEMA = 'myukm_test' AND TABLE_NAME = 'groups'";
    
    $result = $conn->query($query);
    
    if ($result === false) {
        die("Error getting columns: " . $conn->error . "\n");
    }
    
    echo "\nColumns:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['COLUMN_NAME']} ({$row['COLUMN_TYPE']})\n";
        echo "  Nullable: {$row['IS_NULLABLE']}\n";
        echo "  Key: {$row['COLUMN_KEY']}\n";
        echo "  Default: " . ($row['COLUMN_DEFAULT'] ?? 'NULL') . "\n";
        echo "  Extra: {$row['EXTRA']}\n\n";
    }
    
    // Get indexes
    $query = "SHOW INDEX FROM `groups`";
    $result = $conn->query($query);
    
    if ($result === false) {
        echo "\nError getting indexes: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo indexes found on 'groups' table\n";
    } else {
        echo "\nIndexes:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- Index: {$row['Key_name']} on {$row['Column_name']}\n";
            echo "  Type: {$row['Index_type']}\n";
            echo "  Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . "\n";
            echo "  Null: {$row['Null']}\n";
            echo "  Index_comment: {$row['Index_comment']}\n\n";
        }
    }
    
    // Check for any data
    $result = $conn->query("SELECT COUNT(*) as count FROM `groups`");
    $row = $result->fetch_assoc();
    echo "\nNumber of records in groups table: " . $row['count'] . "\n";
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
