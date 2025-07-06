<?php

/**
 * File Organization Utility
 * Helps organize and clean up project files
 */

echo "==============================================\n";
echo "         MyUKM File Organization Tool\n";
echo "==============================================\n\n";

$projectRoot = __DIR__ . '/../../';

// Files to move to scripts/deprecated
$deprecatedFiles = [
    'check-group-urls.bat',
    'check-middleware.bat', 
    'check-status.bat',
    'create-shortcuts.bat',
    'fix-realtime.bat',
    'go.bat',
    'instant-launch.bat',
    'launch-myukm.bat',
    'one-click.bat',
    'one-command.cmd',
    'open-myukm.bat',
    'organize-files-v2.bat',
    'organize-files.bat',
    'quick-fix-echo.bat',
    'quick-start.bat',
    'server-menu.bat',
    'show-run-commands.bat',
    'start-dev-server.bat',
    'start-full-dev.bat',
    'start-production-like.bat',
    'start-queue-worker.bat',
    'start-realtime-dev.bat',
    'start-server.bat',
    'test-axios-fix.bat',
    'test-broadcast-optimization.bat',
    'test-broadcast-optimization.ps1',
    'test-broadcast-optimizations.bat',
    'test-broadcast-simple.bat',
    'test-channel-fix.bat',
    'test-launcher.bat',
    'test-manual-realtime.bat',
    'test-middleware-fix.bat',
    'test-middleware-fix.ps1',
    'test-performance-fix.bat',
    'test-realtime-fixes.bat',
    'test-realtime-responsiveness.bat',
    'ultra-launch.bat',
    'verify-realtime-final.bat'
];

// Create deprecated folder
$deprecatedDir = $projectRoot . 'scripts/deprecated/';
if (!is_dir($deprecatedDir)) {
    mkdir($deprecatedDir, 0755, true);
    echo "📁 Created scripts/deprecated/ directory\n";
}

echo "[1/3] Moving deprecated batch files...\n";
$movedCount = 0;

foreach ($deprecatedFiles as $file) {
    $sourcePath = $projectRoot . $file;
    $targetPath = $deprecatedDir . $file;
    
    if (file_exists($sourcePath)) {
        if (rename($sourcePath, $targetPath)) {
            echo "  ✅ Moved: {$file}\n";
            $movedCount++;
        } else {
            echo "  ❌ Failed to move: {$file}\n";
        }
    }
}

echo "📦 Moved {$movedCount} deprecated files\n\n";

echo "[2/3] Cleaning up temporary files...\n";
$tempFiles = glob($projectRoot . '*.tmp');
$tempFiles = array_merge($tempFiles, glob($projectRoot . 'count()'));
$tempFiles = array_merge($tempFiles, glob($projectRoot . "'Test"));
$tempFiles = array_merge($tempFiles, glob($projectRoot . "'TEST123'])"));

$cleanedCount = 0;
foreach ($tempFiles as $tempFile) {
    if (unlink($tempFile)) {
        echo "  🗑️  Removed: " . basename($tempFile) . "\n";
        $cleanedCount++;
    }
}

echo "🧹 Cleaned {$cleanedCount} temporary files\n\n";

echo "[3/3] Creating .gitignore entries for organization...\n";

$gitignoreAdditions = "\n# Organized project structure\n";
$gitignoreAdditions .= "scripts/deprecated/\n";
$gitignoreAdditions .= "*.tmp\n";
$gitignoreAdditions .= "'Test\n";
$gitignoreAdditions .= "'TEST123'])\n";

$gitignorePath = $projectRoot . '.gitignore';
if (file_exists($gitignorePath)) {
    $gitignoreContent = file_get_contents($gitignorePath);
    if (strpos($gitignoreContent, '# Organized project structure') === false) {
        file_put_contents($gitignorePath, $gitignoreAdditions, FILE_APPEND);
        echo "  ✅ Updated .gitignore\n";
    } else {
        echo "  ℹ️  .gitignore already updated\n";
    }
}

echo "\n==============================================\n";
echo "         File Organization Complete\n";
echo "==============================================\n";
echo "\n📋 Summary:\n";
echo "  • Moved {$movedCount} deprecated files to scripts/deprecated/\n";
echo "  • Cleaned {$cleanedCount} temporary files\n";
echo "  • Updated .gitignore\n";
echo "\n✨ Project is now better organized!\n";
echo "   Use the new start.bat for all development tasks.\n\n";
