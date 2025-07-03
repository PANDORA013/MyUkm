<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test users for different roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test users...');

        // Create admin website
        $adminWebsite = User::firstOrCreate([
            'nim' => 'admin001'
        ], [
            'name' => 'Admin Website',
            'password' => Hash::make('password'),
            'role' => 'admin_website'
        ]);
        $this->info("✓ Admin Website: {$adminWebsite->name} (password: password)");

        // Create admin grup
        $adminGrup = User::firstOrCreate([
            'nim' => 'admin002'
        ], [
            'name' => 'Admin Grup',
            'password' => Hash::make('password'),
            'role' => 'admin_grup'
        ]);
        $this->info("✓ Admin Grup: {$adminGrup->name} (password: password)");

        // Create regular user
        $user = User::firstOrCreate([
            'nim' => '123456789'
        ], [
            'name' => 'User Test',
            'password' => Hash::make('password'),
            'role' => 'member'
        ]);
        $this->info("✓ Regular User: {$user->name} (password: password)");

        // Create test group
        $group = Group::firstOrCreate([
            'referral_code' => '0810'
        ], [
            'name' => 'Test UKM',
            'description' => 'Test UKM for development'
        ]);
        $this->info("✓ Test Group: {$group->name} (code: {$group->referral_code})");

        // Attach admin grup to group
        if (!$adminGrup->groups()->where('group_id', $group->id)->exists()) {
            $adminGrup->groups()->attach($group->id);
            $this->info("✓ Admin grup attached to test group");
        }

        // Attach regular user to group
        if (!$user->groups()->where('group_id', $group->id)->exists()) {
            $user->groups()->attach($group->id);
            $this->info("✓ Regular user attached to test group");
        }

        $this->info("\nTest users created successfully!");
        $this->info("You can now login with:");
        $this->info("- Admin Website: nim=admin001, password=password");
        $this->info("- Admin Grup: nim=admin002, password=password");
        $this->info("- Regular User: nim=123456789, password=password");
        $this->info("\nTest Group Code: 0810");
    }
}
