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
    
    // Check if groups table has referral_code column
    $result = $conn->query("SHOW COLUMNS FROM `groups` LIKE 'referral_code'");
    
    if ($result === false) {
        die("Error checking for referral_code column: " . $conn->error);
    }
    
    if ($result->num_rows === 0) {
        die("The 'referral_code' column does not exist in the 'groups' table.\n");
    }
    
    // Get column details
    $column = $result->fetch_assoc();
    
    echo "\n=== referral_code Column Details ===\n";
    echo "Type: {$column['Type']}\n";
    echo "Null: {$column['Null']}\n";
    echo "Key: {$column['Key']}\n";
    echo "Default: " . ($column['Default'] === null ? 'NULL' : $column['Default']) . "\n";
    echo "Extra: {$column['Extra']}\n";
    
    // Check for unique constraint
    $result = $conn->query("SHOW INDEX FROM `groups` WHERE Column_name = 'referral_code' AND Key_name != 'PRIMARY'");
    
    if ($result === false) {
        echo "\nError checking for indexes: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo unique constraint found on 'referral_code' column.\n";
    } else {
        $index = $result->fetch_assoc();
        echo "\n=== Index on referral_code ===\n";
        echo "Name: {$index['Key_name']}\n";
        echo "Unique: " . ($index['Non_unique'] ? 'No' : 'Yes') . "\n";
        echo "Type: {$index['Index_type']}\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
