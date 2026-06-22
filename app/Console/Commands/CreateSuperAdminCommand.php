<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperAdminCommand extends Command
{
    protected $signature = 'admin:create-super
                            {--name=  : Full name}
                            {--email= : Email address}
                            {--password= : Password}';

    protected $description = 'Create or update a super admin user (safe for production)';

    public function handle(): int
    {
        $name     = $this->option('name');
        $email    = $this->option('email');
        $password = $this->option('password');

        if (! $name || ! $email || ! $password) {
            $this->error('All three options are required: --name, --email, --password');
            return self::FAILURE;
        }

        // Create the admin role if it does not already exist
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Create or update the user without touching any other data
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('admin');

        $this->info('Super admin ready.');
        $this->line("  Name:  {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Role:  admin");

        return self::SUCCESS;
    }
}
