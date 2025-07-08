<?php

// Database configuration
$config = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'myukm_test',
    'port' => 3306
];

// Function to run a MySQL query and return the result as an array
function runQuery($conn, $query) {
    $result = $conn->query($query);
    if ($result === false) {
        return ['error' => $conn->error];
    }
    
    $rows = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

try {
    // Create connection
    $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database'], $config['port']);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "=== Database Connection Successful ===\n\n";
    
    // Get database version
    $version = runQuery($conn, "SELECT VERSION() AS version")[0]['version'];
    echo "MySQL Version: $version\n\n";
    
    // List all tables
    echo "=== Database Tables ===\n";
    $tables = runQuery($conn, "SHOW TABLES");
    
    foreach ($tables as $table) {
        $tableName = reset($table);
        echo "\nTable: $tableName\n";
        
        // Get column count
        $columns = runQuery($conn, "SHOW COLUMNS FROM `$tableName`");
        echo "  Columns: " . count($columns) . "\n";
        
        // Get row count
        $count = runQuery($conn, "SELECT COUNT(*) AS c FROM `$tableName`");
        echo "  Rows: " . ($count[0]['c'] ?? 0) . "\n";
    }
    
    // Check groups table specifically
    echo "\n=== Groups Table Structure ===\n";
    
    // Get table structure
    $columns = runQuery($conn, "SHOW COLUMNS FROM `groups`");
    
    if (empty($columns)) {
        echo "The 'groups' table does not exist or has no columns.\n";
    } else {
        echo "\nColumns in 'groups' table:\n";
        echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 5) . str_pad("Key", 10) . "Default\n";
        echo str_repeat("-", 60) . "\n";
        
        foreach ($columns as $col) {
            echo str_pad($col['Field'], 20) . 
                 str_pad($col['Type'], 20) . 
                 str_pad($col['Null'], 5) . 
                 str_pad($col['Key'], 10) . 
                 ($col['Default'] ?? 'NULL') . "\n";
        }
    }
    
    // Check for indexes on groups table
    echo "\nIndexes on 'groups' table:\n";
    $indexes = runQuery($conn, "SHOW INDEX FROM `groups`");
    
    if (isset($indexes['error'])) {
        echo "Error getting indexes: " . $indexes['error'] . "\n";
    } else if (empty($indexes)) {
        echo "No indexes found on 'groups' table\n";
    } else {
        $currentIndex = '';
        
        foreach ($indexes as $index) {
            if ($currentIndex !== $index['Key_name']) {
                if ($currentIndex !== '') echo "\n";
                $currentIndex = $index['Key_name'];
                echo "- {$index['Key_name']}:";
                if ($index['Non_unique'] == 0) echo " UNIQUE";
                if ($index['Index_type'] !== 'BTREE') echo " ({$index['Index_type']})";
                echo "\n";
            }
            echo "  {$index['Column_name']} ({$index['Seq_in_index']})\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
