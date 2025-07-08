<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {--filter= : Filter which tests to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the application tests using PHPUnit';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $command = [
            PHP_BINARY,
            'vendor/phpunit/phpunit/phpunit',
            '--colors=always'
        ];

        if ($filter = $this->option('filter')) {
            $command[] = '--filter';
            $command[] = $filter;
        }

        $process = new Process($command, base_path());
        $process->setTimeout(null);

        try {
            $process->setTty(Process::isTtySupported());
        } catch (\RuntimeException $e) {
            $this->output->writeln('Warning: ' . $e->getMessage());
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });

        return $process->getExitCode();
    }
}
