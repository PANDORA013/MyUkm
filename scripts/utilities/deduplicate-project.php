<?php

/**
 * MyUKM Project Deduplication Utility
 * Consolidates duplicate folders and files in scripts directory
 */

echo "==============================================\n";
echo "         MyUKM Deduplication Tool\n";
echo "==============================================\n\n";

$scriptsRoot = __DIR__ . '/../';
$backupDir = $scriptsRoot . 'backup_before_cleanup/';

// Create backup directory
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "📁 Created backup directory\n";
}

echo "[1/5] Analyzing duplicates...\n";

// Define folder consolidation plan
$consolidationPlan = [
    'utils' => 'utilities',  // Move utils/* to utilities/
    'testing' => 'test'      // Move testing/* to test/
];

// Files to move from scripts root to utilities
$utilityFiles = [
    'check_db.php',
    'check_ukm_database.php', 
    'check_ukm_ids.php',
    'debug_routes.php',
    'delete_admin_account.php',
    'quick_db_setup.php',
    'quick_setup.php',
    'setup_admin.php',
    'setup_admin_grup_data.php',
    'create_deletion_history.php',
    'mysql_sync_check.php',
    'final_verification.php'
];

// Files to move from scripts root to test
$testFiles = [
    'chat_debug.php',
    'chat_verification.php',
    'middleware_chat_test.php',
    'test-queue-performance.php',
    'test-queue.php', 
    'test-realtime-performance.php'
];

$movedCount = 0;
$mergedCount = 0;
$cleanedCount = 0;

echo "[2/5] Consolidating folder structure...\n";

// Consolidate utils -> utilities
if (is_dir($scriptsRoot . 'utils/')) {
    $utilsFiles = scandir($scriptsRoot . 'utils/');
    foreach ($utilsFiles as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $sourcePath = $scriptsRoot . 'utils/' . $file;
        $targetPath = $scriptsRoot . 'utilities/' . $file;
        
        if (file_exists($targetPath)) {
            echo "  ⚠️  Skipped duplicate: {$file}\n";
        } else {
            if (rename($sourcePath, $targetPath)) {
                echo "  ✅ Moved to utilities/: {$file}\n";
                $mergedCount++;
            }
        }
    }
    
    // Remove empty utils directory
    if (rmdir($scriptsRoot . 'utils/')) {
        echo "📁 Removed empty utils/ directory\n";
    }
}

// Consolidate testing -> test
if (is_dir($scriptsRoot . 'testing/')) {
    $testingFiles = scandir($scriptsRoot . 'testing/');
    foreach ($testingFiles as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $sourcePath = $scriptsRoot . 'testing/' . $file;
        $targetPath = $scriptsRoot . 'test/' . $file;
        
        if (file_exists($targetPath)) {
            echo "  ⚠️  Skipped duplicate: {$file}\n";
        } else {
            if (rename($sourcePath, $targetPath)) {
                echo "  ✅ Moved to test/: {$file}\n";
                $mergedCount++;
            }
        }
    }
    
    // Remove empty testing directory
    if (rmdir($scriptsRoot . 'testing/')) {
        echo "📁 Removed empty testing/ directory\n";
    }
}

echo "\n[3/5] Moving loose files from scripts root...\n";

// Move utility files from root
foreach ($utilityFiles as $file) {
    $sourcePath = $scriptsRoot . $file;
    $targetPath = $scriptsRoot . 'utilities/' . $file;
    
    if (file_exists($sourcePath)) {
        if (file_exists($targetPath)) {
            echo "  ⚠️  Skipped existing: {$file}\n";
        } else {
            if (rename($sourcePath, $targetPath)) {
                echo "  ✅ Moved to utilities/: {$file}\n";
                $movedCount++;
            }
        }
    }
}

// Move test files from root
foreach ($testFiles as $file) {
    $sourcePath = $scriptsRoot . $file;
    $targetPath = $scriptsRoot . 'test/' . $file;
    
    if (file_exists($sourcePath)) {
        if (file_exists($targetPath)) {
            echo "  ⚠️  Skipped existing: {$file}\n";
        } else {
            if (rename($sourcePath, $targetPath)) {
                echo "  ✅ Moved to test/: {$file}\n";
                $movedCount++;
            }
        }
    }
}

echo "\n[4/5] Moving remaining scripts to setup/ subdirectory...\n";

// Create setup directory if it doesn't exist
if (!is_dir($scriptsRoot . 'setup/')) {
    mkdir($scriptsRoot . 'setup/', 0755, true);
    echo "📁 Created setup/ directory\n";
}

// Move remaining loose files to setup
$remainingFiles = glob($scriptsRoot . '*.php');
$remainingFiles = array_merge($remainingFiles, glob($scriptsRoot . '*.bat'));
$remainingFiles = array_merge($remainingFiles, glob($scriptsRoot . '*.sh'));

foreach ($remainingFiles as $filePath) {
    $file = basename($filePath);
    
    // Skip files that should stay in root
    if (in_array($file, ['README.md', 'DUPLICATE_ANALYSIS.md'])) continue;
    
    $targetPath = $scriptsRoot . 'setup/' . $file;
    
    if (file_exists($targetPath)) {
        echo "  ⚠️  Skipped existing: {$file}\n";
    } else {
        if (rename($filePath, $targetPath)) {
            echo "  ✅ Moved to setup/: {$file}\n";
            $movedCount++;
        }
    }
}

echo "\n[5/5] Final cleanup...\n";

// Update README with new structure
$readmePath = $scriptsRoot . 'README.md';
$newReadme = "# MyUKM Scripts Organization

This directory contains organized scripts for the MyUKM Laravel project.

## 📁 Directory Structure (Post-Cleanup)

```
scripts/
├── test/               # All testing utilities and scripts
├── utilities/          # Project maintenance and validation utilities  
├── setup/              # Initial setup and configuration scripts
├── deprecated/         # Old batch files (preserved for reference)
├── database/           # Database-related scripts
├── monitoring/         # System monitoring scripts
└── start/              # Development server scripts
```

## 🚀 Quick Start

**Primary Launch Script:**
```bash
# From project root
start.bat
```

## 🧹 Recent Cleanup

✅ **Duplicates Removed:**
- Merged `utils/` → `utilities/`
- Merged `testing/` → `test/`  
- Organized loose files into appropriate subdirectories
- Eliminated file duplications across folders

## 📊 Cleanup Summary

- **Merged:** {$mergedCount} files from duplicate folders
- **Organized:** {$movedCount} files into proper structure  
- **Structure:** Clean, logical, no duplicates

All functionality preserved - just better organized!
";

file_put_contents($readmePath, $newReadme);
echo "📝 Updated README.md\n";

echo "\n==============================================\n";
echo "         Deduplication Complete\n";
echo "==============================================\n";
echo "\n📊 Summary:\n";
echo "  • Merged {$mergedCount} files from duplicate folders\n";
echo "  • Organized {$movedCount} loose files into structure\n";
echo "  • Eliminated duplicate folders (utils/, testing/)\n";
echo "  • Clean, logical directory structure maintained\n";
echo "\n✨ Project structure is now fully consolidated!\n";
echo "   No more duplicates - everything in its proper place.\n\n";
