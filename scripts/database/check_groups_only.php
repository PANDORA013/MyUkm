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

try {
    // Create connection
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "=== Checking Groups Table ===\n\n";
    
    // Check if groups table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'groups'")->rowCount() > 0;
    
    if (!$tableExists) {
        die("The 'groups' table does not exist.\n");
    }
    
    // Get table structure
    echo "=== Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE `groups`");
    
    echo str_pad("Field", 20) . str_pad("Type", 25) . str_pad("Null", 5) . str_pad("Key", 10) . "Default\n";
    echo str_repeat("-", 70) . "\n";
    
    while ($row = $stmt->fetch()) {
        $default = $row['Default'] === null ? 'NULL' : "'{$row['Default']}'";
        echo str_pad($row['Field'], 20) . 
             str_pad($row['Type'], 25) . 
             str_pad($row['Null'], 5) . 
             str_pad($row['Key'], 10) . 
             $default . "\n";
    }
    
    // Get indexes
    echo "\n=== Indexes ===\n";
    $indexes = $pdo->query("SHOW INDEX FROM `groups`")->fetchAll();
    
    if (empty($indexes)) {
        echo "No indexes found.\n";
    } else {
        $currentIndex = '';
        
        foreach ($indexes as $index) {
            if ($currentIndex !== $index['Key_name']) {
                if ($currentIndex !== '') echo "\n";
                $currentIndex = $index['Key_name'];
                echo "- {$index['Key_name']}";
                if ($index['Non_unique'] == 0) echo " (UNIQUE)";
                echo "\n  Type: {$index['Index_type']}\n  Columns: ";
            } else {
                echo ", ";
            }
            echo $index['Column_name'];
        }
        echo "\n";
    }
    
    // Get foreign keys
    echo "\n=== Foreign Keys ===\n";
    $fks = $pdo->query("
        SELECT 
            COLUMN_NAME, 
            CONSTRAINT_NAME, 
            REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE 
            TABLE_SCHEMA = 'myukm_test'
            AND TABLE_NAME = 'groups'
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll();
    
    if (empty($fks)) {
        echo "No foreign keys found.\n";
    } else {
        foreach ($fks as $fk) {
            echo "- {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']})\n";
        }
    }
    
    // Get table status
    $status = $pdo->query("SHOW TABLE STATUS LIKE 'groups'")->fetch();
    
    echo "\n=== Table Status ===\n";
    echo "Rows: " . number_format($status['Rows'], 0) . "\n";
    echo "Data Length: " . round($status['Data_length'] / 1024, 2) . " KB\n";
    echo "Index Length: " . round($status['Index_length'] / 1024, 2) . " KB\n";
    echo "Engine: {$status['Engine']}\n";
    echo "Collation: {$status['Collation']}\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}

echo "\n";
