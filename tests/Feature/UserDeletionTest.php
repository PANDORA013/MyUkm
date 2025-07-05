<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserDeletion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected $adminWebsite;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin website user
        $this->adminWebsite = User::create([
            'name' => 'Admin Website',
            'nim' => 'ADMIN001',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin_website'
        ]);
        
        // Create regular user
        $this->regularUser = User::create([
            'name' => 'Regular User',
            'nim' => 'USER001',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'member'
        ]);
    }

    /** @test */
    public function admin_website_can_delete_user_and_create_deletion_record()
    {
        // Login as admin website
        $this->actingAs($this->adminWebsite);
        
        // Delete the regular user
        $response = $this->delete(route('admin.hapus-member', $this->regularUser->id));
        
        // Should redirect back with success message
        $response->assertRedirect();
        
        // User should be deleted
        $this->assertDatabaseMissing('users', ['id' => $this->regularUser->id]);
        
        // Deletion record should be created
        $this->assertDatabaseHas('user_deletions', [
            'deleted_user_id' => $this->regularUser->id,
            'deleted_user_name' => 'Regular User',
            'deleted_user_nim' => 'USER001',
            'deleted_user_email' => 'user@example.com',
            'deleted_user_role' => 'member',
            'deletion_reason' => 'Dihapus oleh admin website',
            'deleted_by' => $this->adminWebsite->id
        ]);
    }

    /** @test */
    public function deleted_user_cannot_login()
    {
        // Create deletion record for the user
        UserDeletion::create([
            'deleted_user_id' => $this->regularUser->id,
            'deleted_user_name' => $this->regularUser->name,
            'deleted_user_nim' => $this->regularUser->nim,
            'deleted_user_email' => $this->regularUser->email,
            'deleted_user_role' => $this->regularUser->role,
            'deletion_reason' => 'Test deletion',
            'deleted_by' => $this->adminWebsite->id
        ]);
        
        // Try to login with deleted user credentials
        $response = $this->post(route('login'), [
            'nim' => 'USER001',
            'password' => 'password'
        ]);
        
        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHasErrors(['nim' => 'Akun ini telah dihapus oleh admin dan tidak dapat digunakan lagi.']);
        
        // Should not be authenticated
        $this->assertGuest();
    }

    /** @test */
    public function admin_website_can_view_deletion_history()
    {
        // Create some deletion records
        UserDeletion::create([
            'deleted_user_id' => $this->regularUser->id,
            'deleted_user_name' => $this->regularUser->name,
            'deleted_user_nim' => $this->regularUser->nim,
            'deleted_user_email' => $this->regularUser->email,
            'deleted_user_role' => $this->regularUser->role,
            'deletion_reason' => 'Test deletion',
            'deleted_by' => $this->adminWebsite->id
        ]);
        
        // Login as admin website
        $this->actingAs($this->adminWebsite);
        
        // Access deletion history page
        $response = $this->get(route('admin.riwayat-penghapusan'));
        
        // Check for errors
        if ($response->status() !== 200) {
            dd($response->content());
        }
        
        // Should see the deletion history
        $response->assertStatus(200);
        $response->assertSee('Riwayat Penghapusan User');
        $response->assertSee('Regular User');
        $response->assertSee('USER001');
        $response->assertSee('Test deletion');
    }

    /** @test */
    public function admin_website_cannot_delete_another_admin_website()
    {
        // Create another admin website
        $anotherAdmin = User::create([
            'name' => 'Another Admin',
            'nim' => 'ADMIN002',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin_website'
        ]);
        
        // Login as first admin
        $this->actingAs($this->adminWebsite);
        
        // Try to delete another admin
        $response = $this->delete(route('admin.hapus-member', $anotherAdmin->id));
        
        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Tidak bisa menghapus akun admin website lain');
        
        // Admin should still exist
        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
        
        // No deletion record should be created
        $this->assertDatabaseMissing('user_deletions', [
            'deleted_user_id' => $anotherAdmin->id
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_other_users()
    {
        // Create another regular user
        $anotherUser = User::create([
            'name' => 'Another User',
            'nim' => 'USER002',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => 'member'
        ]);
        
        // Login as regular user
        $this->actingAs($this->regularUser);
        
        // Try to delete another user
        $response = $this->delete(route('admin.hapus-member', $anotherUser->id));
        
        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda tidak memiliki izin untuk menghapus akun');
        
        // User should still exist
        $this->assertDatabaseHas('users', ['id' => $anotherUser->id]);
        
        // No deletion record should be created
        $this->assertDatabaseMissing('user_deletions', [
            'deleted_user_id' => $anotherUser->id
        ]);
    }
}
