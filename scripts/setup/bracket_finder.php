<?php

/*
 * Precise JavaScript Bracket Finder
 * Identifies exact locations of mismatched brackets
 */

echo "\n=== PRECISE BRACKET ANALYSIS ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

$chatFile = __DIR__ . '/../resources/views/chat.blade.php';
$content = file_get_contents($chatFile);
$lines = explode("\n", $content);

// Track brackets with their exact positions
$brackets = ['(' => ')', '[' => ']', '{' => '}'];
$stack = [];
$issues = [];

foreach ($lines as $lineNum => $line) {
    $realLineNum = $lineNum + 1;
    $chars = str_split($line);
    
    foreach ($chars as $charPos => $char) {
        if (in_array($char, array_keys($brackets))) {
            // Opening bracket
            $stack[] = [
                'type' => $char,
                'line' => $realLineNum,
                'char' => $charPos,
                'context' => trim($line)
            ];
        } elseif (in_array($char, array_values($brackets))) {
            // Closing bracket
            $expectedOpener = array_search($char, $brackets);
            
            if (empty($stack)) {
                $issues[] = [
                    'type' => 'extra_closing',
                    'bracket' => $char,
                    'line' => $realLineNum,
                    'char' => $charPos,
                    'context' => trim($line)
                ];
            } else {
                $lastOpen = array_pop($stack);
                if ($lastOpen['type'] !== $expectedOpener) {
                    $issues[] = [
                        'type' => 'mismatched',
                        'bracket' => $char,
                        'expected' => $brackets[$lastOpen['type']],
                        'line' => $realLineNum,
                        'char' => $charPos,
                        'context' => trim($line),
                        'opened_at' => $lastOpen
                    ];
                    // Put it back since it doesn't match
                    $stack[] = $lastOpen;
                }
            }
        }
    }
}

// Report unclosed brackets
foreach ($stack as $unclosed) {
    $issues[] = [
        'type' => 'unclosed',
        'bracket' => $unclosed['type'],
        'line' => $unclosed['line'],
        'char' => $unclosed['char'],
        'context' => $unclosed['context']
    ];
}

echo "--- BRACKET ISSUES FOUND ---\n";

if (empty($issues)) {
    echo "✅ No bracket issues found!\n";
} else {
    foreach ($issues as $issue) {
        switch ($issue['type']) {
            case 'extra_closing':
                echo "❌ Extra closing '{$issue['bracket']}' at line {$issue['line']}, char {$issue['char']}\n";
                echo "   Context: {$issue['context']}\n\n";
                break;
                
            case 'mismatched':
                echo "❌ Mismatched bracket '{$issue['bracket']}' at line {$issue['line']}, char {$issue['char']}\n";
                echo "   Expected: '{$issue['expected']}'\n";
                echo "   Context: {$issue['context']}\n";
                echo "   Opened at line {$issue['opened_at']['line']}: {$issue['opened_at']['context']}\n\n";
                break;
                
            case 'unclosed':
                echo "❌ Unclosed '{$issue['bracket']}' at line {$issue['line']}, char {$issue['char']}\n";
                echo "   Context: {$issue['context']}\n\n";
                break;
        }
    }
}

echo "--- SUGGESTIONS ---\n";

$lineNumbers = [];
foreach ($issues as $issue) {
    $lineNumbers[] = $issue['line'];
}
$lineNumbers = array_unique($lineNumbers);

if (!empty($lineNumbers)) {
    echo "Check these specific lines:\n";
    foreach ($lineNumbers as $lineNum) {
        echo "  Line $lineNum: " . trim($lines[$lineNum - 1]) . "\n";
    }
} else {
    echo "✅ No specific lines need attention\n";
}

echo "\n=== BRACKET ANALYSIS COMPLETE ===\n";
