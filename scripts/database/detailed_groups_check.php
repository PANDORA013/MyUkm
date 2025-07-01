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
    
    // Get table structure using information_schema
    $query = "
        SELECT 
            COLUMN_NAME, 
            COLUMN_TYPE, 
            IS_NULLABLE, 
            COLUMN_KEY, 
            COLUMN_DEFAULT, 
            EXTRA,
            CHARACTER_MAXIMUM_LENGTH,
            COLLATION_NAME
        FROM 
            information_schema.COLUMNS 
        WHERE 
            TABLE_SCHEMA = 'myukm_test' 
            AND TABLE_NAME = 'groups'
        ORDER BY 
            ORDINAL_POSITION
    ";
    
    $result = $conn->query($query);
    
    if ($result === false) {
        die("Error getting table structure: " . $conn->error . "\n");
    }
    
    echo "=== Groups Table Structure ===\n\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "Column: {$row['COLUMN_NAME']}\n";
        echo "  Type: {$row['COLUMN_TYPE']}\n";
        echo "  Nullable: {$row['IS_NULLABLE']}\n";
        echo "  Key: " . ($row['COLUMN_KEY'] ?: 'None') . "\n";
        echo "  Default: " . ($row['COLUMN_DEFAULT'] === null ? 'NULL' : $row['COLUMN_DEFAULT']) . "\n";
        echo "  Extra: " . ($row['EXTRA'] ?: 'None') . "\n";
        if ($row['CHARACTER_MAXIMUM_LENGTH'] !== null) {
            echo "  Max Length: {$row['CHARACTER_MAXIMUM_LENGTH']}\n";
        }
        if ($row['COLLATION_NAME'] !== null) {
            echo "  Collation: {$row['COLLATION_NAME']}\n";
        }
        echo "\n";
    }
    
    // Get index information
    $query = "
        SELECT 
            INDEX_NAME,
            COLUMN_NAME,
            NON_UNIQUE,
            INDEX_TYPE,
            COMMENT
        FROM 
            information_schema.STATISTICS
        WHERE 
            TABLE_SCHEMA = 'myukm_test'
            AND TABLE_NAME = 'groups'
        ORDER BY
            INDEX_NAME,
            SEQ_IN_INDEX
    ";
    
    $result = $conn->query($query);
    
    if ($result === false) {
        echo "\nError getting index information: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo indexes found on 'groups' table\n";
    } else {
        $currentIndex = '';
        
        while ($row = $result->fetch_assoc()) {
            if ($currentIndex !== $row['INDEX_NAME']) {
                if ($currentIndex !== '') echo "\n";
                $currentIndex = $row['INDEX_NAME'];
                echo "Index: {$row['INDEX_NAME']}\n";
                echo "  Type: {$row['INDEX_TYPE']}\n";
                echo "  Unique: " . ($row['NON_UNIQUE'] ? 'No' : 'Yes') . "\n";
                echo "  Columns: ";
            } else {
                echo ", ";
            }
            echo $row['COLUMN_NAME'];
        }
        echo "\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
