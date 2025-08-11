<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UKM;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin {--nim=TH.171004} {--name=Administrator} {--password=AR.171004}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for MyUKM system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nim = $this->option('nim');
        $name = $this->option('name');
        $password = $this->option('password');

        // Create a default UKM if none exists
        $ukm = UKM::firstOrCreate(
            ['code' => 'ADMIN'],
            [
                'name' => 'Administrator UKM',
                'description' => 'Default UKM for system administrators'
            ]
        );

        // Check if admin already exists
        $existingUser = User::where('nim', $nim)->first();
        if ($existingUser) {
            $this->error("User with NIM {$nim} already exists!");
            return Command::FAILURE;
        }

        // Create admin user with unique email
        $email = strtolower(str_replace('.', '', $nim)) . '@myukm.com';
        $user = User::create([
            'name' => $name,
            'nim' => $nim,
            'email' => $email,
            'password' => Hash::make($password),
            'password_plain' => $password, // Store plain password for reference
            'role' => 'admin',
            'ukm_id' => $ukm->id,
        ]);

        $this->info('Admin user created successfully!');
        $this->line('');
        $this->line('=== ADMIN LOGIN DETAILS ===');
        $this->line("NIM: {$nim}");
        $this->line("Name: {$name}");
        $this->line("Email: {$email}");
        $this->line("Password: {$password}");
        $this->line("Role: admin");
        $this->line("UKM: {$ukm->name}");
        $this->line('');
        $this->line('You can now login to MyUKM system with these credentials.');

        return Command::SUCCESS;
    }
}
