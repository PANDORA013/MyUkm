<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip browser tests if Chrome driver is not available
        if (!$this->canRunBrowserTests()) {
            $this->markTestSkipped('Browser tests require Chromedriver to be running');
        }
    }
    
    private function canRunBrowserTests(): bool
    {
        // Check if we can connect to Chromedriver port
        $connection = @fsockopen('localhost', 9515, $errno, $errstr, 1);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }
    /**
     * A basic browser test example.
     */
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel');
        });
    }
}
