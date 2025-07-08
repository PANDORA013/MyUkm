<?php

// Database configuration
$config = [
    'host' => '127.0.0.1',
    'username' => 'root',
    'password' => '',
    'database' => 'myukm_test',
    'port' => 3306
];

// Function to run a MySQL command and return the output
function runMysqlCommand($command, $config) {
    $cmd = sprintf(
        'mysql -h %s -u %s %s -P %d %s -e "%s" 2>&1',
        escapeshellarg($config['host']),
        escapeshellarg($config['username']),
        $config['password'] ? '-p' . escapeshellarg($config['password']) : '',
        $config['port'],
        escapeshellarg($config['database']),
        str_replace('"', '\"', $command)
    );
    
    exec($cmd, $output, $returnVar);
    
    if ($returnVar !== 0) {
        return ['error' => 'Command failed: ' . implode("\n", $output)];
    }
    
    return $output;
}

echo "=== Checking Database Structure ===\n\n";

try {
    // Test connection and get version
    $result = runMysqlCommand('SELECT VERSION()', $config);
    
    if (isset($result['error'])) {
        die("Error connecting to database: " . $result['error'] . "\n");
    }
    
    echo "MySQL Version: " . $result[1] . "\n\n";
    
    // List all tables
    echo "=== Database Tables ===\n";
    $tables = runMysqlCommand('SHOW TABLES', $config);
    
    if (isset($tables['error'])) {
        die("Error getting tables: " . $tables['error'] . "\n");
    }
    
    // Skip the first line (header)
    array_shift($tables);
    
    if (empty($tables)) {
        echo "No tables found in the database.\n";
    } else {
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    }
    
    // Check groups table
    echo "\n=== Groups Table Structure ===\n";
    $structure = runMysqlCommand('DESCRIBE `groups`', $config);
    
    if (isset($structure['error'])) {
        echo "Error getting groups table structure: " . $structure['error'] . "\n";
    } else {
        echo "\nColumns in 'groups' table:\n";
        foreach ($structure as $line) {
            echo "- $line\n";
        }
    }
    
    // Get table creation SQL
    echo "\n=== CREATE TABLE Statement ===\n";
    $createTable = runMysqlCommand('SHOW CREATE TABLE `groups`', $config);
    
    if (isset($createTable['error'])) {
        echo "Error getting CREATE TABLE statement: " . $createTable['error'] . "\n";
    } else {
        // Skip the first line (header)
        array_shift($createTable);
        echo implode("\n", $createTable) . "\n";
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
