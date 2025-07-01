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
        'mysql -h %s -u %s %s -P %d -e "%s" --batch --skip-column-names 2>&1',
        escapeshellarg($config['host']),
        escapeshellarg($config['username']),
        $config['password'] ? '-p' . escapeshellarg($config['password']) : '',
        $config['port'],
        str_replace('"', '\"', $command)
    );
    
    exec($cmd, $output, $returnVar);
    
    if ($returnVar !== 0) {
        return ['error' => 'Command failed: ' . implode("\n", $output)];
    }
    
    return $output;
}

try {
    echo "=== Checking Database Structure ===\n\n";
    
    // Test connection
    $result = runMysqlCommand('SELECT VERSION()', $config);
    
    if (isset($result['error'])) {
        die("Error connecting to database: " . $result['error'] . "\n");
    }
    
    echo "MySQL Version: " . $result[0] . "\n\n";
    
    // List all tables
    echo "=== Database Tables ===\n";
    $tables = runMysqlCommand('SHOW TABLES', $config);
    
    if (isset($tables['error'])) {
        die("Error getting tables: " . $tables['error'] . "\n");
    }
    
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check groups table
    echo "\n=== Groups Table Structure ===\n";
    $structure = runMysqlCommand('DESCRIBE `groups`', $config);
    
    if (isset($structure['error'])) {
        echo "Error getting groups table structure: " . $structure['error'] . "\n";
    } else {
        echo "\nColumns in 'groups' table:\n";
        foreach ($structure as $line) {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 4) {
                echo "- " . str_pad($parts[0], 15) . " " . str_pad($parts[1], 20) . " " . 
                     str_pad($parts[2], 5) . " " . str_pad($parts[3], 10) . " " . 
                     (isset($parts[4]) ? $parts[4] : '') . "\n";
            } else {
                echo "- $line\n";
            }
        }
    }
    
    // Check indexes
    echo "\n=== Indexes on 'groups' table ===\n";
    $indexes = runMysqlCommand('SHOW INDEX FROM `groups`', $config);
    
    if (isset($indexes['error'])) {
        echo "Error getting indexes: " . $indexes['error'] . "\n";
    } else if (empty($indexes)) {
        echo "No indexes found on 'groups' table\n";
    } else {
        $currentIndex = '';
        foreach ($indexes as $line) {
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 3) {
                if ($currentIndex !== $parts[2]) {
                    $currentIndex = $parts[2];
                    echo "\n- $currentIndex";
                    if (isset($parts[1]) && $parts[1] === '0') {
                        echo " (UNIQUE)";
                    }
                    echo ":\n";
                }
                echo "  - " . $parts[4] . " (Seq: " . $parts[3] . ")\n";
            } else {
                echo "- $line\n";
            }
        }
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
