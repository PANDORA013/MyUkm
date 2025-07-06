<?php
echo "========================================\n";
echo "   MyUKM - Check Group Chat URLs\n";
echo "========================================\n\n";

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$groups = \App\Models\Group::all();

echo "📍 Available Groups and Chat URLs:\n\n";

foreach ($groups as $group) {
    echo "   • Group: {$group->name} (ID: {$group->id})\n";
    echo "     Code: {$group->referral_code}\n";
    echo "     Chat URL: http://localhost:8000/ukm/{$group->referral_code}/chat\n";
    echo "     Members: " . $group->users()->count() . "\n\n";
}

echo "========================================\n";
echo "   💡 How to Access Chat:\n";
echo "========================================\n";
echo "1. Make sure you're logged in\n";
echo "2. Make sure you're a member of the group\n";
echo "3. Use the correct referral code in URL\n";
echo "4. Example: http://localhost:8000/ukm/0810/chat\n\n";

echo "========================================\n";
echo "   🔧 Quick Actions:\n";
echo "========================================\n";
echo "• Login: http://localhost:8000/login\n";
echo "• UKM List: http://localhost:8000/ukm\n";
echo "• Dashboard: http://localhost:8000/dashboard\n\n";
?>
