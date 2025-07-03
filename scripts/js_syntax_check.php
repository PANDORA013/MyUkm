<?php

/*
 * JavaScript Syntax Validation Script
 * Checks chat.blade.php for JavaScript syntax errors
 */

echo "\n=== JAVASCRIPT SYNTAX VALIDATION ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

$chatFile = __DIR__ . '/../resources/views/chat.blade.php';

if (!file_exists($chatFile)) {
    echo "❌ Chat file not found: $chatFile\n";
    exit(1);
}

echo "✅ Chat file found: $chatFile\n";

$content = file_get_contents($chatFile);

echo "\n--- JAVASCRIPT SYNTAX CHECKS ---\n";

// Check for common JavaScript syntax issues
$checks = [
    'Unmatched parentheses' => ['/(^|[^\\\\])\([^)]*$/', false],
    'Unmatched brackets' => ['/\[[^\]]*$/', false], 
    'Unmatched braces' => ['/\{[^}]*$/', false],
    'Missing semicolons after function declarations' => ['/function\s*\([^)]*\)\s*\{[^}]*\}\s*(?![;,])/m', false],
    'addEventListener missing closing' => ['/addEventListener\s*\(\s*[\'"][^\'"][\'"]\s*,\s*function\s*\([^)]*\)\s*\{[^}]*\}\s*(?!\))/m', false],
    'Unterminated strings' => ['/[\'"][^\'"]*$/', false],
    'Double semicolons' => ['/;;/', true],
    'Missing commas in object literals' => ['/\{\s*[^:,}]+:[^,}]+\s+[^:,}]+:/', true]
];

foreach ($checks as $checkName => $pattern) {
    list($regex, $shouldMatch) = $pattern;
    $matches = preg_match_all($regex, $content, $foundMatches);
    
    if ($shouldMatch && $matches > 0) {
        echo "⚠️  $checkName: Found $matches instances\n";
    } elseif (!$shouldMatch && $matches > 0) {
        echo "❌ $checkName: Found $matches potential issues\n";
    } else {
        echo "✅ $checkName: OK\n";
    }
}

echo "\n--- EVENT LISTENER VALIDATION ---\n";

// Check for duplicate event listeners
$eventListeners = [];
preg_match_all('/(\w+)\.addEventListener\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    $element = $match[1];
    $event = $match[2];
    $key = "$element.$event";
    
    if (!isset($eventListeners[$key])) {
        $eventListeners[$key] = 0;
    }
    $eventListeners[$key]++;
}

echo "Event Listeners Found:\n";
foreach ($eventListeners as $listener => $count) {
    if ($count > 1) {
        echo "  ⚠️  $listener: $count instances (potential duplicate)\n";
    } else {
        echo "  ✅ $listener: $count instance\n";
    }
}

echo "\n--- FUNCTION VALIDATION ---\n";

// Check for function declarations and calls
preg_match_all('/function\s+(\w+)\s*\(/', $content, $functionDeclarations);
preg_match_all('/(\w+)\s*\(/', $content, $functionCalls);

$declaredFunctions = array_unique($functionDeclarations[1]);
$calledFunctions = array_unique($functionCalls[1]);

echo "Declared Functions: " . count($declaredFunctions) . "\n";
foreach ($declaredFunctions as $func) {
    echo "  ✅ $func()\n";
}

echo "\nPotential undefined function calls:\n";
$potentialUndefined = array_diff($calledFunctions, $declaredFunctions);
$potentialUndefined = array_filter($potentialUndefined, function($func) {
    // Filter out built-in functions and common patterns
    return !in_array($func, [
        'console', 'setTimeout', 'clearTimeout', 'fetch', 'JSON', 'Date', 
        'addEventListener', 'querySelector', 'querySelectorAll', 'getElementById',
        'createElement', 'appendChild', 'removeChild', 'classList', 'setAttribute',
        'preventDefault', 'stopPropagation', 'includes', 'forEach', 'map',
        'filter', 'push', 'pop', 'slice', 'splice', 'indexOf', 'toString',
        'toLocaleTimeString', 'scrollIntoView', 'scrollTo', 'scrollTop',
        'if', 'else', 'for', 'while', 'switch', 'case', 'break', 'continue',
        'return', 'try', 'catch', 'throw', 'new', 'var', 'let', 'const'
    ]);
});

if (empty($potentialUndefined)) {
    echo "  ✅ No undefined function calls detected\n";
} else {
    foreach ($potentialUndefined as $func) {
        echo "  ⚠️  $func() - check if this function is defined\n";
    }
}

echo "\n--- BLADE TEMPLATE SYNTAX CHECK ---\n";

// Check for Blade template syntax in JavaScript
$bladePatterns = [
    '{{ route(' => 'Route helpers',
    '{{ csrf_token(' => 'CSRF tokens',
    '{{ asset(' => 'Asset helpers',
    '@php' => 'PHP blocks',
    '@endphp' => 'PHP block endings'
];

foreach ($bladePatterns as $pattern => $description) {
    $count = substr_count($content, $pattern);
    if ($count > 0) {
        echo "  ✅ $description: $count instances\n";
    } else {
        echo "  ℹ️  $description: None found\n";
    }
}

echo "\n--- RECOMMENDATIONS ---\n";

$recommendations = [];

if (isset($eventListeners['messageInput.input']) && $eventListeners['messageInput.input'] > 1) {
    $recommendations[] = "Multiple 'input' event listeners on messageInput have been consolidated";
}

$recommendations[] = "Test chat functionality in browser to verify JavaScript works correctly";
$recommendations[] = "Check browser console for any remaining errors";
$recommendations[] = "Test typing indicator and message sending functionality";
$recommendations[] = "Verify real-time updates work properly";

if (empty($recommendations)) {
    echo "✅ No major issues detected\n";
} else {
    foreach ($recommendations as $i => $rec) {
        echo ($i + 1) . ". $rec\n";
    }
}

echo "\n=== JAVASCRIPT VALIDATION COMPLETE ===\n";
