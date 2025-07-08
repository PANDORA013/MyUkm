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

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / (1 << (10 * $pow)), $precision) . ' ' . $units[$pow];
}

echo "=== Database Structure Check ===\n\n";

try {
    // Create PDO connection
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    
    // Get database version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MySQL Version: $version\n";
    
    // Get database size
    $dbSize = $pdo->query("
        SELECT 
            table_schema AS 'Database', 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
        FROM information_schema.tables 
        WHERE table_schema = '{$config['database']}'
    ")->fetch();
    
    echo "Database: {$dbSize['Database']} (" . number_format($dbSize['Size (MB)'], 2) . " MB)\n\n";
    
    // List all tables
    echo "=== Database Tables ===\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "No tables found in the database.\n";
    } else {
        foreach ($tables as $table) {
            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $size = $pdo->query("
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                FROM information_schema.TABLES 
                WHERE table_schema = '{$config['database']}'
                AND table_name = '$table'
            ")->fetchColumn();
            
            echo "- $table ($count rows, " . number_format($size, 2) . " MB)\n";
        }
    }
    
    // Check groups table specifically
    echo "\n=== Groups Table Structure ===\n";
    
    if (!in_array('groups', $tables)) {
        echo "The 'groups' table does not exist.\n";
    } else {
        // Get table structure
        $columns = $pdo->query("SHOW COLUMNS FROM `groups`")->fetchAll();
        
        echo "\nColumns:\n";
        foreach ($columns as $col) {
            $default = $col['Default'] === null ? 'NULL' : "'{$col['Default']}'";
            echo sprintf(
                "- %-20s %-20s %-5s %-10s %-10s %s\n",
                $col['Field'],
                $col['Type'],
                $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL',
                $col['Key'] ?: '-',
                $col['Extra'] ?: '-',
                "Default: $default"
            );
        }
        
        // Get indexes
        $indexes = $pdo->query("SHOW INDEX FROM `groups`")->fetchAll();
        
        if (empty($indexes)) {
            echo "\nNo indexes found on 'groups' table\n";
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
            
            echo "\nIndexes:\n";
            foreach ($indexGroups as $name => $index) {
                echo "- $name\n";
                echo "  Type: {$index['type']}\n";
                echo "  Unique: " . ($index['unique'] ? 'Yes' : 'No') . "\n";
                echo "  Columns: " . implode(', ', $index['columns']) . "\n\n";
            }
        }
        
        // Get table status
        $status = $pdo->query("SHOW TABLE STATUS LIKE 'groups'")->fetch();
        
        echo "\nTable Status:\n";
        echo "- Rows: " . number_format($status['Rows'], 0) . "\n";
        echo "- Data Length: " . formatBytes($status['Data_length']) . "\n";
        echo "- Index Length: " . formatBytes($status['Index_length']) . "\n";
        echo "- Engine: {$status['Engine']}\n";
        echo "- Collation: {$status['Collation']}\n";
        
        // Get foreign keys
        $foreignKeys = $pdo->query("
            SELECT 
                COLUMN_NAME, 
                CONSTRAINT_NAME, 
                REFERENCED_TABLE_NAME, 
                REFERENCED_COLUMN_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE 
                TABLE_SCHEMA = '{$config['database']}'
                AND TABLE_NAME = 'groups'
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll();
        
        if (!empty($foreignKeys)) {
            echo "\nForeign Keys:\n";
            foreach ($foreignKeys as $fk) {
                echo "- {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']})\n";
            }
        }
    }
    
    echo "\n=== Check Complete ===\n";
    
} catch (PDOException $e) {
    die("\nDatabase Error: " . $e->getMessage() . "\n");
}

echo "\n";
