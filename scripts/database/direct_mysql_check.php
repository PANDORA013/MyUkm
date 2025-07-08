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
    $result = $conn->query("SELECT VERSION() AS version");
    $version = $result->fetch_assoc()['version'];
    echo "MySQL Version: $version\n\n";
    
    // List all tables
    echo "=== Database Tables ===\n";
    $tables = $conn->query("SHOW TABLES");
    
    while ($table = $tables->fetch_row()) {
        $tableName = $table[0];
        echo "\nTable: $tableName\n";
        
        // Get column count
        $columns = $conn->query("SHOW COLUMNS FROM `$tableName`");
        echo "  Columns: " . $columns->num_rows . "\n";
        
        // Get row count
        $count = $conn->query("SELECT COUNT(*) AS c FROM `$tableName`")->fetch_assoc()['c'];
        echo "  Rows: $count\n";
    }
    
    // Check groups table specifically
    echo "\n=== Groups Table Details ===\n";
    
    // Check if groups table exists
    $result = $conn->query("SHOW TABLES LIKE 'groups'");
    if ($result->num_rows === 0) {
        die("The 'groups' table does not exist.\n");
    }
    
    // Get table structure
    echo "\nTable Structure:\n";
    $result = $conn->query("DESCRIBE `groups`");
    
    if ($result === false) {
        echo "Error describing table: " . $conn->error . "\n";
    } else {
        echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 5) . str_pad("Key", 10) . "Default\n";
        echo str_repeat("-", 60) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['Field'], 20) . 
                 str_pad($row['Type'], 20) . 
                 str_pad($row['Null'], 5) . 
                 str_pad($row['Key'], 10) . 
                 ($row['Default'] ?? 'NULL') . "\n";
        }
    }
    
    // Check for indexes on groups table
    echo "\nIndexes:\n";
    $result = $conn->query("SHOW INDEX FROM `groups`");
    
    if ($result === false) {
        echo "Error getting indexes: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "No indexes found on 'groups' table\n";
    } else {
        echo str_pad("Key Name", 30) . str_pad("Column", 20) . str_pad("Unique", 10) . "Type\n";
        echo str_repeat("-", 70) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            echo str_pad($row['Key_name'], 30) . 
                 str_pad($row['Column_name'], 20) . 
                 str_pad(($row['Non_unique'] ? 'No' : 'Yes'), 10) . 
                 $row['Index_type'] . "\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
