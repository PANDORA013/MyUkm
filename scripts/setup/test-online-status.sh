#!/bin/bash

# Script untuk test sinkronisasi status online anggota UKM
# Pastikan server Laravel dan database sudah berjalan

echo "=== Test Sinkronisasi Status Online Anggota UKM ==="
echo ""

# Test 1: Periksa route tersedia
echo "1. Memeriksa route online status..."
php artisan route:list --name=chat.online-members
php artisan route:list --name=chat.update-online-status
echo ""

# Test 2: Periksa model method
echo "2. Memeriksa User model methods..."
php artisan tinker --execute="
\$user = App\Models\User::first();
if (\$user) {
    echo 'User found: ' . \$user->name . PHP_EOL;
    echo 'Is online: ' . (\$user->isOnline() ? 'Yes' : 'No') . PHP_EOL;
    
    \$group = App\Models\Group::first();
    if (\$group) {
        echo 'Group found: ' . \$group->name . PHP_EOL;
        \$onlineMembers = App\Models\User::getOnlineMembersInGroup(\$group->id);
        echo 'Online members in group: ' . \$onlineMembers->count() . PHP_EOL;
        
        \$totalMembers = App\Models\User::getOnlineCountInGroup(\$group->id);
        echo 'Online count: ' . \$totalMembers . PHP_EOL;
    }
}
"
echo ""

# Test 3: Update last_seen_at untuk simulasi user online
echo "3. Simulasi user online..."
php artisan tinker --execute="
\$user = App\Models\User::first();
if (\$user) {
    \$user->update(['last_seen_at' => now()]);
    echo 'Updated last_seen_at for user: ' . \$user->name . PHP_EOL;
    echo 'Is online now: ' . (\$user->fresh()->isOnline() ? 'Yes' : 'No') . PHP_EOL;
}
"
echo ""

# Test 4: Test event broadcasting (jika Pusher tersedia)
echo "4. Test event broadcasting..."
php artisan tinker --execute="
\$group = App\Models\Group::first();
if (\$group) {
    \$onlineMembers = App\Models\User::getOnlineMembersInGroup(\$group->id);
    \$totalMembers = \$group->users()->count();
    
    echo 'Broadcasting online status for group: ' . \$group->name . PHP_EOL;
    echo 'Online members: ' . \$onlineMembers->count() . PHP_EOL;
    echo 'Total members: ' . \$totalMembers . PHP_EOL;
    
    try {
        event(new App\Events\UserOnlineStatusChanged(
            1, 
            'Test User', 
            true, 
            \$group->code, 
            \$onlineMembers->toArray(), 
            \$totalMembers
        ));
        echo 'Event broadcast successful!' . PHP_EOL;
    } catch (Exception \$e) {
        echo 'Event broadcast failed: ' . \$e->getMessage() . PHP_EOL;
    }
}
"
echo ""

echo "=== Test Selesai ==="
echo ""
echo "Untuk test lengkap:"
echo "1. Buka browser ke halaman chat UKM"
echo "2. Buka tab/window baru dengan user lain"
echo "3. Perhatikan status online yang tersinkronisasi"
echo "4. Check console browser untuk log status online"
