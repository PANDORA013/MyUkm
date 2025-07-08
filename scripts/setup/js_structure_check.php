<?php

/*
 * Enhanced JavaScript Structure Validation
 * Specifically checks for balanced brackets, parentheses, and braces
 */

echo "\n=== ENHANCED JAVASCRIPT STRUCTURE VALIDATION ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

$chatFile = __DIR__ . '/../resources/views/chat.blade.php';
$content = file_get_contents($chatFile);

// Extract JavaScript content (between <script> tags)
preg_match_all('/<script[^>]*>(.*?)<\/script>/s', $content, $matches);
$jsContent = implode("\n", $matches[1]);

if (empty($jsContent)) {
    echo "❌ No JavaScript content found in <script> tags\n";
    // Check for inline JavaScript
    if (strpos($content, 'addEventListener') !== false) {
        echo "ℹ️  Found JavaScript code outside <script> tags, analyzing full content\n";
        $jsContent = $content;
    } else {
        exit(1);
    }
}

echo "✅ JavaScript content extracted for analysis\n";
echo "Content length: " . strlen($jsContent) . " characters\n\n";

// Remove comments and strings to avoid false positives
$cleanContent = preg_replace('/\/\*.*?\*\//s', '', $jsContent); // Remove /* */ comments
$cleanContent = preg_replace('/\/\/.*?$/m', '', $cleanContent); // Remove // comments
$cleanContent = preg_replace('/"(?:[^"\\\\]|\\\\.)*"/', '""', $cleanContent); // Remove double quoted strings
$cleanContent = preg_replace("/'(?:[^'\\\\]|\\\\.)*'/", "''", $cleanContent); // Remove single quoted strings
$cleanContent = preg_replace('/`(?:[^`\\\\]|\\\\.)*`/', '``', $cleanContent); // Remove template literals

echo "--- BRACKET BALANCE ANALYSIS ---\n";

// Check for balanced brackets, parentheses, and braces
$brackets = ['(' => ')', '[' => ']', '{' => '}'];
$stacks = ['(' => [], '[' => [], '{' => []];
$errors = [];

$lines = explode("\n", $cleanContent);
$lineNumber = 0;

foreach ($lines as $line) {
    $lineNumber++;
    $chars = str_split($line);
    
    foreach ($chars as $charIndex => $char) {
        if (in_array($char, array_keys($brackets))) {
            // Opening bracket
            $stacks[$char][] = ['line' => $lineNumber, 'char' => $charIndex];
        } elseif (in_array($char, array_values($brackets))) {
            // Closing bracket
            $opener = array_search($char, $brackets);
            if (!empty($stacks[$opener])) {
                array_pop($stacks[$opener]);
            } else {
                $errors[] = "Extra closing '$char' at line $lineNumber, char $charIndex";
            }
        }
    }
}

// Check for unclosed brackets
foreach ($stacks as $bracket => $stack) {
    if (!empty($stack)) {
        foreach ($stack as $item) {
            $errors[] = "Unclosed '$bracket' at line {$item['line']}, char {$item['char']}";
        }
    }
}

if (empty($errors)) {
    echo "✅ All brackets are properly balanced\n";
} else {
    echo "❌ Found bracket balance issues:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

echo "\n--- FUNCTION STRUCTURE ANALYSIS ---\n";

// Check function structures
preg_match_all('/function\s+(\w+)\s*\([^)]*\)\s*\{/', $cleanContent, $functionMatches, PREG_OFFSET_CAPTURE);
preg_match_all('/(\w+)\s*\.\s*addEventListener\s*\(\s*[\'"][^\'"]+[\'"]\s*,\s*function\s*\([^)]*\)\s*\{/', $cleanContent, $eventMatches, PREG_OFFSET_CAPTURE);

echo "Function declarations found: " . count($functionMatches[1]) . "\n";
foreach ($functionMatches[1] as $match) {
    echo "  ✅ function {$match[0]}()\n";
}

echo "\nEvent listener functions found: " . count($eventMatches[1]) . "\n";
foreach ($eventMatches[1] as $match) {
    echo "  ✅ {$match[0]}.addEventListener\n";
}

echo "\n--- ASYNC/AWAIT STRUCTURE ANALYSIS ---\n";

// Check async/await patterns
preg_match_all('/async\s+function\s+(\w+)/', $cleanContent, $asyncFunctions);
preg_match_all('/await\s+(\w+)/', $cleanContent, $awaitCalls);

echo "Async functions: " . count($asyncFunctions[1]) . "\n";
foreach ($asyncFunctions[1] as $func) {
    echo "  ✅ async function $func\n";
}

echo "Await calls: " . count($awaitCalls[1]) . "\n";

echo "\n--- PROMISE CHAIN ANALYSIS ---\n";

// Check for proper promise chains
$thenCount = substr_count($cleanContent, '.then(');
$catchCount = substr_count($cleanContent, '.catch(');
$finallyCount = substr_count($cleanContent, '.finally(');

echo "Promise chains found:\n";
echo "  .then() calls: $thenCount\n";
echo "  .catch() calls: $catchCount\n";
echo "  .finally() calls: $finallyCount\n";

if ($thenCount > 0 && $catchCount == 0) {
    echo "  ⚠️  Promise chains without .catch() may cause unhandled rejections\n";
} else {
    echo "  ✅ Promise chains appear to have proper error handling\n";
}

echo "\n--- COMMON SYNTAX ERROR PATTERNS ---\n";

$syntaxPatterns = [
    '/\(\s*\)/' => 'Empty parentheses',
    '/\{\s*\}/' => 'Empty braces',
    '/,\s*[,}]/' => 'Extra commas',
    '/;\s*;/' => 'Double semicolons',
    '/\)\s*{/' => 'Function definition patterns',
    '/}\s*else/' => 'Else clause patterns',
    '/}\s*catch/' => 'Catch clause patterns'
];

foreach ($syntaxPatterns as $pattern => $description) {
    $matches = preg_match_all($pattern, $cleanContent);
    if ($matches > 0) {
        echo "  ✅ $description: $matches found (normal)\n";
    }
}

echo "\n--- SPECIFIC ERROR PATTERNS ---\n";

$errorPatterns = [
    '/\)\s*\)\s*[^;,}]/' => 'Double closing parentheses',
    '/}\s*}\s*[^;,]/' => 'Double closing braces',
    '/,\s*}/' => 'Trailing comma before closing brace',
    '/\.\s*\.\s*/' => 'Double dots (possible syntax error)',
    '/=\s*=\s*=\s*[^=]/' => 'Triple equals (normal)',
    '/[^=]=\s*[^=]/' => 'Single equals in comparison'
];

$foundErrors = false;
foreach ($errorPatterns as $pattern => $description) {
    $matches = preg_match_all($pattern, $cleanContent);
    if ($matches > 0 && !in_array($description, ['Triple equals (normal)'])) {
        echo "  ❌ $description: $matches found\n";
        $foundErrors = true;
    } elseif ($matches > 0) {
        echo "  ✅ $description: $matches found (OK)\n";
    }
}

if (!$foundErrors) {
    echo "  ✅ No common error patterns detected\n";
}

echo "\n--- RECOMMENDATIONS ---\n";

$recommendations = [];

if (!empty($errors)) {
    $recommendations[] = "Fix bracket balance issues identified above";
}

if ($thenCount > 0 && $catchCount == 0) {
    $recommendations[] = "Add .catch() handlers to promise chains for better error handling";
}

$recommendations[] = "Test the chat functionality in browser to verify JavaScript works";
$recommendations[] = "Check browser console for any runtime errors";
$recommendations[] = "Verify all AJAX requests work properly";

foreach ($recommendations as $i => $rec) {
    echo ($i + 1) . ". $rec\n";
}

echo "\n=== ENHANCED STRUCTURE VALIDATION COMPLETE ===\n";
