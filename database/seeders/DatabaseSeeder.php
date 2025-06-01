<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chat;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        $user1 = User::create([
            'name' => 'John Doe',
            'nim' => '12345',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'nim' => '12346',
            'password' => Hash::make('password123'),
        ]);

        // Create groups
        $simsGroup = Group::create([
            'name' => 'SIMS',
            'referral_code' => '0812'
        ]);

        $psmGroup = Group::create([
            'name' => 'PSM',
            'referral_code' => '0813'
        ]);

        // Attach users to groups
        $user1->groups()->attach([$simsGroup->id, $psmGroup->id]);
        $user2->groups()->attach($simsGroup->id);

        // Create some test chat messages
        Chat::create([
            'user_id' => $user1->id,
            'group_code' => '0812',
            'message' => 'Hello SIMS members!'
        ]);

        Chat::create([
            'user_id' => $user2->id,
            'group_code' => '0812',
            'message' => 'Hi there from SIMS!'
        ]);

        Chat::create([
            'user_id' => $user1->id,
            'group_code' => '0813',
            'message' => 'Hello PSM members!'
        ]);
    }
}
