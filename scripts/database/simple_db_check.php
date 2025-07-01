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
    
    echo "=== Connected to MySQL ===\n";
    
    // Get list of tables
    $tables = $conn->query("SHOW TABLES");
    
    if ($tables === false) {
        die("Error getting tables: " . $conn->error);
    }
    
    echo "\n=== Tables in database ===\n";
    
    while ($table = $tables->fetch_row()) {
        $tableName = $table[0];
        echo "\nTable: $tableName\n";
        
        // Get column count
        $columns = $conn->query("SHOW COLUMNS FROM `$tableName`");
        
        if ($columns === false) {
            echo "  Error getting columns: " . $conn->error . "\n";
            continue;
        }
        
        echo "  Columns: " . $columns->num_rows . "\n";
        
        // Get row count
        $count = $conn->query("SELECT COUNT(*) as c FROM `$tableName`");
        if ($count !== false) {
            $row = $count->fetch_assoc();
            echo "  Rows: " . $row['c'] . "\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
