<?php

namespace Tests\TestListeners;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class TestFailedListener implements TestListener
{
    public function addError(Test $test, \Throwable $t, float $time): void
    {
        // Handle test error
    }

    public function addWarning(Test $test, Warning $e, float $time): void
    {
        // Handle test warning
    }

    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        // Handle test failure
    }

    public function addIncompleteTest(Test $test, \Throwable $t, float $time): void
    {
        // Handle incomplete test
    }

    public function addRiskyTest(Test $test, \Throwable $t, float $time): void
    {
        // Handle risky test
    }

    public function addSkippedTest(Test $test, \Throwable $t, float $time): void
    {
        // Handle skipped test
    }

    public function startTestSuite(TestSuite $suite): void
    {
        // Handle test suite start
    }

    public function endTestSuite(TestSuite $suite): void
    {
        // Handle test suite end
    }

    public function startTest(Test $test): void
    {
        // Handle test start
    }

    public function endTest(Test $test, float $time): void
    {
        // Handle test end
    }
}
