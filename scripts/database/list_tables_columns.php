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
    
    // Get all tables
    $tables = $conn->query("SHOW TABLES");
    
    while ($table = $tables->fetch_row()) {
        $tableName = $table[0];
        echo "\n=== Table: $tableName ===\n";
        
        // Get columns for this table
        $columns = $conn->query("SHOW COLUMNS FROM `$tableName`");
        
        if ($columns === false) {
            echo "  Error getting columns: " . $conn->error . "\n";
            continue;
        }
        
        while ($col = $columns->fetch_assoc()) {
            echo "- {$col['Field']} ({$col['Type']})\n";
            echo "  Null: {$col['Null']}\n";
            echo "  Key: {$col['Key']}\n";
            echo "  Default: " . ($col['Default'] === null ? 'NULL' : $col['Default']) . "\n";
            echo "  Extra: {$col['Extra']}\n\n";
        }
        
        // Get indexes for this table
        $indexes = $conn->query("SHOW INDEX FROM `$tableName`");
        
        if ($indexes !== false && $indexes->num_rows > 0) {
            echo "Indexes:\n";
            $currentIndex = '';
            
            while ($idx = $indexes->fetch_assoc()) {
                if ($currentIndex !== $idx['Key_name']) {
                    if ($currentIndex !== '') echo "\n";
                    $currentIndex = $idx['Key_name'];
                    echo "  - {$idx['Key_name']}:";
                    if ($idx['Non_unique'] == 0) echo " UNIQUE";
                    if ($idx['Index_type'] !== 'BTREE') echo " ({$idx['Index_type']})";
                    echo "\n";
                }
                echo "      {$idx['Column_name']} ({$idx['Seq_in_index']})\n";
            }
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
