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
    echo "MySQL Version: $version\n\n";
    
    // Check if groups table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'groups'");
    if ($stmt->rowCount() === 0) {
        die("The 'groups' table does not exist.\n");
    }
    
    // Get table structure
    echo "=== Groups Table Structure ===\n\n";
    
    // Get columns
    $stmt = $pdo->query("SHOW COLUMNS FROM `groups`");
    echo "Columns:\n";
    foreach ($stmt as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
        echo "  Null: {$column['Null']}\n";
        echo "  Key: " . ($column['Key'] ?: 'None') . "\n";
        echo "  Default: " . ($column['Default'] === null ? 'NULL' : $column['Default']) . "\n";
        echo "  Extra: " . ($column['Extra'] ?: 'None') . "\n\n";
    }
    
    // Get indexes
    $stmt = $pdo->query("SHOW INDEX FROM `groups`");
    $indexes = [];
    
    while ($row = $stmt->fetch()) {
        $indexName = $row['Key_name'];
        if (!isset($indexes[$indexName])) {
            $indexes[$indexName] = [
                'unique' => !$row['Non_unique'],
                'type' => $row['Index_type'],
                'columns' => []
            ];
        }
        $indexes[$indexName]['columns'][$row['Seq_in_index']] = $row['Column_name'];
    }
    
    if (empty($indexes)) {
        echo "No indexes found on 'groups' table\n";
    } else {
        echo "\nIndexes:\n";
        foreach ($indexes as $name => $index) {
            echo "- $name\n";
            echo "  Type: {$index['type']}\n";
            echo "  Unique: " . ($index['unique'] ? 'Yes' : 'No') . "\n";
            echo "  Columns: " . implode(', ', $index['columns']) . "\n\n";
        }
    }
    
    // Check for foreign keys
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
        echo "\nForeign Keys:\n";
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
