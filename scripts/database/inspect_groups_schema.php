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
    
    // Get database and server info
    $serverInfo = $conn->query("SELECT VERSION() as version")->fetch_assoc();
    echo "MySQL Version: " . $serverInfo['version'] . "\n";
    
    // Check if groups table exists
    $tableExists = $conn->query("SHOW TABLES LIKE 'groups'");
    if ($tableExists->num_rows === 0) {
        die("\nThe 'groups' table does not exist.\n");
    }
    
    // Get table creation SQL
    echo "\n=== Groups Table Creation SQL ===\n";
    $createTable = $conn->query("SHOW CREATE TABLE `groups`");
    if ($createTable) {
        $row = $createTable->fetch_assoc();
        echo $row['Create Table'] . "\n";
    } else {
        echo "Could not retrieve table creation SQL: " . $conn->error . "\n";
    }
    
    // Get table status
    echo "\n=== Table Status ===\n";
    $status = $conn->query("SHOW TABLE STATUS LIKE 'groups'");
    if ($status) {
        $row = $status->fetch_assoc();
        echo "Engine: {$row['Engine']}\n";
        echo "Row Format: {$row['Row_format']}\n";
        echo "Rows: {$row['Rows']}\n";
        echo "Avg Row Length: {$row['Avg_row_length']}\n";
        echo "Data Length: {$row['Data_length']} bytes\n";
        echo "Index Length: {$row['Index_length']} bytes\n";
        echo "Create Time: {$row['Create_time']}\n";
        echo "Update Time: {$row['Update_time']}\n";
        echo "Collation: {$row['Collation']}\n";
    }
    
    // Get column information
    echo "\n=== Column Information ===\n";
    $columns = $conn->query("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'myukm_test' AND TABLE_NAME = 'groups' ORDER BY ORDINAL_POSITION");
    while ($col = $columns->fetch_assoc()) {
        echo "\nColumn: {$col['COLUMN_NAME']}\n";
        echo "  Type: {$col['COLUMN_TYPE']}\n";
        echo "  Nullable: {$col['IS_NULLABLE']}\n";
        echo "  Default: " . ($col['COLUMN_DEFAULT'] === null ? 'NULL' : $col['COLUMN_DEFAULT']) . "\n";
        echo "  Extra: {$col['EXTRA']}\n";
        echo "  Privileges: {$col['PRIVILEGES']}\n";
        echo "  Comment: {$col['COLUMN_COMMENT']}\n";
    }
    
    // Get index information
    echo "\n=== Index Information ===\n";
    $indexes = $conn->query("SHOW INDEX FROM `groups`");
    $currentIndex = '';
    while ($index = $indexes->fetch_assoc()) {
        if ($currentIndex !== $index['Key_name']) {
            if ($currentIndex !== '') echo "\n";
            $currentIndex = $index['Key_name'];
            echo "Index: {$index['Key_name']}\n";
            echo "  Type: {$index['Index_type']}\n";
            echo "  Unique: " . ($index['Non_unique'] ? 'No' : 'Yes') . "\n";
            echo "  Columns: ";
        }
        echo $index['Column_name'] . "(" . $index['Seq_in_index'] . ") ";
    }
    echo "\n";
    
    // Check for foreign keys
    echo "\n=== Foreign Key Constraints ===\n";
    $fks = $conn->query("
        SELECT 
            CONSTRAINT_NAME, 
            COLUMN_NAME, 
            REFERENCED_TABLE_NAME, 
            REFERENCED_COLUMN_NAME,
            UPDATE_RULE,
            DELETE_RULE
        FROM 
            information_schema.KEY_COLUMN_USAGE
        WHERE 
            TABLE_SCHEMA = 'myukm_test'
            AND TABLE_NAME = 'groups'
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    if ($fks->num_rows === 0) {
        echo "No foreign key constraints found.\n";
    } else {
        while ($fk = $fks->fetch_assoc()) {
            echo "- {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}({$fk['REFERENCED_COLUMN_NAME']})\n";
            echo "  On Update: {$fk['UPDATE_RULE']}, On Delete: {$fk['DELETE_RULE']}\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
