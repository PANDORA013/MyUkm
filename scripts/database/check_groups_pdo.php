<?php

// Database configuration
$config = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'myukm_test',
    'port' => 3306,
    'charset' => 'utf8mb4'
];

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Create PDO connection
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    
    echo "=== Database Connection Successful ===\n\n";
    
    // Get database version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: $version\n";
    
    // Check if groups table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'groups'")->rowCount() > 0;
    
    if (!$tableExists) {
        die("\nThe 'groups' table does not exist.\n");
    }
    
    echo "\n=== Groups Table Exists ===\n";
    
    // Get table structure
    echo "\n=== Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE `groups`");
    $columns = $stmt->fetchAll();
    
    if (empty($columns)) {
        echo "No columns found in 'groups' table\n";
    } else {
        echo str_pad("Field", 20) . str_pad("Type", 25) . str_pad("Null", 5) . str_pad("Key", 10) . "Default\n";
        echo str_repeat("-", 70) . "\n";
        
        foreach ($columns as $col) {
            echo str_pad($col['Field'], 20) . 
                 str_pad($col['Type'], 25) . 
                 str_pad($col['Null'], 5) . 
                 str_pad($col['Key'], 10) . 
                 ($col['Default'] ?? 'NULL') . "\n";
        }
    }
    
    // Check for indexes
    echo "\n=== Indexes ===\n";
    $stmt = $pdo->query("SHOW INDEX FROM `groups`");
    $indexes = $stmt->fetchAll();
    
    if (empty($indexes)) {
        echo "No indexes found on 'groups' table\n";
    } else {
        $indexGroups = [];
        
        foreach ($indexes as $index) {
            $name = $index['Key_name'];
            if (!isset($indexGroups[$name])) {
                $indexGroups[$name] = [
                    'unique' => !$index['Non_unique'],
                    'type' => $index['Index_type'],
                    'columns' => []
                ];
            }
            $indexGroups[$name]['columns'][$index['Seq_in_index']] = $index['Column_name'];
        }
        
        foreach ($indexGroups as $name => $index) {
            echo "- $name\n";
            echo "  Type: {$index['type']}\n";
            echo "  Unique: " . ($index['unique'] ? 'Yes' : 'No') . "\n";
            echo "  Columns: " . implode(', ', $index['columns']) . "\n\n";
        }
    }
    
    // Check for foreign keys
    echo "=== Foreign Keys ===\n";
    $stmt = $pdo->query("
        SELECT 
            TABLE_NAME, COLUMN_NAME, 
            CONSTRAINT_NAME, REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE 
            TABLE_SCHEMA = 'myukm_test'
            AND TABLE_NAME = 'groups'
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    $foreignKeys = $stmt->fetchAll();
    
    if (empty($foreignKeys)) {
        echo "No foreign key constraints found on 'groups' table\n";
    } else {
        foreach ($foreignKeys as $fk) {
            echo "- {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']})\n";
        }
    }
    
    // Check for any data in the table
    $count = $pdo->query("SELECT COUNT(*) FROM `groups`")->fetchColumn();
    echo "\nNumber of records in groups table: $count\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
