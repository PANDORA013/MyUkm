<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        // Skip ChromeDriver setup in CI environment or testing environment
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing' ||
            isset($_ENV['CI']) || 
            isset($_ENV['GITHUB_ACTIONS'])) {
            return;
        }
        
        // Check if Chromedriver exists before starting
        $chromedriverPath = base_path('vendor/laravel/dusk/bin/chromedriver-win.exe');
        if (!file_exists($chromedriverPath)) {
            // Skip if Chromedriver is not installed
            return;
        }
        
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        // Check if we're in a testing environment where browser tests should be skipped
        if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing') {
            $this->markTestSkipped('Browser tests are skipped in testing environment');
        }
        
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
