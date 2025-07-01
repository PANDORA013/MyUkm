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
    
    // Check if groups table exists
    $result = $conn->query("SHOW TABLES LIKE 'groups'");
    
    if ($result->num_rows === 0) {
        die("The 'groups' table does not exist.\n");
    }
    
    echo "\n=== Groups Table Structure ===\n";
    
    // Get table structure
    $result = $conn->query("DESCRIBE `groups`");
    
    if ($result === false) {
        die("Error describing table: " . $conn->error);
    }
    
    echo "\nColumns in 'groups' table:\n";
    echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 5) . str_pad("Key", 10) . "Default\n";
    echo str_repeat("-", 60) . "\n";
    
    while ($row = $result->fetch_assoc()) {
        echo str_pad($row['Field'], 20) . 
             str_pad($row['Type'], 20) . 
             str_pad($row['Null'], 5) . 
             str_pad($row['Key'], 10) . 
             $row['Default'] . "\n";
    }
    
    // Check for indexes on referral_code
    $result = $conn->query("SHOW INDEX FROM `groups` WHERE Column_name = 'referral_code'");
    
    if ($result === false) {
        echo "\nError checking indexes: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo indexes found on 'referral_code' column\n";
    } else {
        echo "\nIndexes on 'referral_code' column:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Key_name']} ({$row['Index_type']})\n";
        }
    }
    
    // Check for foreign key constraints
    $result = $conn->query("
        SELECT 
            TABLE_NAME, COLUMN_NAME, 
            CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE 
            TABLE_SCHEMA = '{$config['database']}'
            AND TABLE_NAME = 'groups'
            AND COLUMN_NAME = 'referral_code'
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    if ($result === false) {
        echo "\nError checking foreign keys: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo foreign key constraints found on 'referral_code' column\n";
    } else {
        echo "\nForeign key constraints on 'referral_code' column:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['CONSTRAINT_NAME']}: {$row['TABLE_NAME']}.{$row['COLUMN_NAME']} ";
            echo "REFERENCES {$row['REFERENCED_TABLE_NAME']}({$row['REFERENCED_COLUMN_NAME']})\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
