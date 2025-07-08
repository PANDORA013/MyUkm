<?php

/**
 * Check Group URLs Utility
 * Validates all group referral codes and their accessibility
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Group;

echo "==============================================\n";
echo "         MyUKM Group URLs Checker\n";
echo "==============================================\n\n";

try {
    // Initialize Laravel app
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    echo "[1/4] Checking database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connected successfully\n\n";

    echo "[2/4] Fetching all groups...\n";
    $groups = Group::all();
    echo "✅ Found " . $groups->count() . " groups\n\n";

    echo "[3/4] Validating group referral codes...\n";
    $validCodes = 0;
    $invalidCodes = 0;

    foreach ($groups as $group) {
        $code = $group->referral_code;
        
        // Check if code is 4-digit numeric
        if (preg_match('/^\d{4}$/', $code)) {
            echo "✅ Group '{$group->name}' - Code: {$code} (VALID)\n";
            $validCodes++;
        } else {
            echo "❌ Group '{$group->name}' - Code: {$code} (INVALID - must be 4 digits)\n";
            $invalidCodes++;
        }
    }

    echo "\n[4/4] Summary:\n";
    echo "✅ Valid codes: {$validCodes}\n";
    echo "❌ Invalid codes: {$invalidCodes}\n";

    if ($invalidCodes > 0) {
        echo "\n⚠️  Some groups have invalid referral codes!\n";
        echo "   Run: php artisan db:seed --class=SyncUkmGroupsSeeder\n";
        echo "   To fix invalid codes.\n";
    } else {
        echo "\n🎉 All group codes are valid!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n==============================================\n";
echo "         Group URLs Check Complete\n";
echo "==============================================\n";
