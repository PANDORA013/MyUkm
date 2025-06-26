<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserGroupRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_attached_to_group()
    {
        $user = User::create([
            'name' => 'Test User',
            'nim' => '12345',
            'password' => bcrypt('password'),
        ]);

        $group = Group::create([
            'name' => 'Test Group',
            'referral_code' => 'T123'
        ]);

        $user->groups()->attach($group->id);

        $this->assertTrue($user->groups()->where('group_id', $group->id)->exists());
    }
}
