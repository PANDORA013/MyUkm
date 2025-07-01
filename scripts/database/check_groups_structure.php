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
    
    // Get table structure
    $result = $conn->query("DESCRIBE `groups`");
    
    if ($result === false) {
        die("Error describing table: " . $conn->error);
    }
    
    echo "\n=== Groups Table Structure ===\n";
    echo str_pad("Field", 20) . str_pad("Type", 20) . str_pad("Null", 5) . str_pad("Key", 10) . "Default\n";
    echo str_repeat("-", 60) . "\n";
    
    while ($row = $result->fetch_assoc()) {
        echo str_pad($row['Field'], 20) . 
             str_pad($row['Type'], 20) . 
             str_pad($row['Null'], 5) . 
             str_pad($row['Key'], 10) . 
             ($row['Default'] ?? 'NULL') . "\n";
    }
    
    // Check for indexes on referral_code
    $result = $conn->query("SHOW INDEX FROM `groups` WHERE Column_name = 'referral_code'");
    
    if ($result === false) {
        echo "\nError checking indexes: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo indexes found on 'referral_code' column\n";
    } else {
        echo "\n=== Indexes on 'referral_code' column ===\n";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Key_name']} ({$row['Index_type']})\n";
            echo "  - Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . "\n";
        }
    }
    
    // Check for any data issues with referral_code
    $result = $conn->query("SELECT COUNT(*) as total, LENGTH(referral_code) as code_length, COUNT(*) as count 
                           FROM `groups` 
                           GROUP BY LENGTH(referral_code)");
    
    if ($result === false) {
        echo "\nError checking referral_code lengths: " . $conn->error . "\n";
    } else if ($result->num_rows === 0) {
        echo "\nNo data found in 'groups' table\n";
    } else {
        echo "\n=== Referral Code Length Distribution ===\n";
        while ($row = $result->fetch_assoc()) {
            echo "- Length {$row['code_length']}: {$row['count']} records\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
