<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Test User')
                    ->type('nim', '12345678')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('Daftar')
                    ->assertPathIs('/dashboard')
                    ->assertAuthenticated();
        });
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'nim' => '12345678',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->visit('/login')
                    ->type('nim', $user->nim)
                    ->type('password', 'password123')
                    ->press('Masuk')
                    ->assertPathIs('/dashboard')
                    ->assertAuthenticatedAs($user);
        });
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->visit('/login')
                    ->type('nim', $user->nim)
                    ->type('password', 'wrong-password')
                    ->press('Masuk')
                    ->assertPathIs('/login')
                    ->assertSee('These credentials do not match our records.');
        });
    }
}
