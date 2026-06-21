<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'treasurer']);
        Role::firstOrCreate(['name' => 'member']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@unityclub.local'],
            [
                'name'              => 'Club Admin',
                'password'          => Hash::make('admin123'),
                'phone'             => '01700000001',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $treasurer = User::firstOrCreate(
            ['email' => 'treasurer@unityclub.local'],
            [
                'name'              => 'Club Treasurer',
                'password'          => Hash::make('treasurer123'),
                'phone'             => '01700000002',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
        $treasurer->syncRoles(['treasurer']);

        $sampleMembers = [
            ['name' => 'Rahim Uddin',   'phone' => '01711000001', 'email' => 'rahim@example.com'],
            ['name' => 'Karim Ahmed',   'phone' => '01711000002', 'email' => 'karim@example.com'],
            ['name' => 'Nasrin Begum',  'phone' => '01711000003', 'email' => 'nasrin@example.com'],
            ['name' => 'Jamal Hossain', 'phone' => '01711000004', 'email' => 'jamal@example.com'],
            ['name' => 'Farida Khatun', 'phone' => '01711000005', 'email' => 'farida@example.com'],
        ];

        foreach ($sampleMembers as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'phone'             => $data['phone'],
                    'password'          => Hash::make('member123'),
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles(['member']);

            Member::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'member_number'      => 'UC-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'join_date'          => now()->subMonths(rand(3, 18))->toDateString(),
                    'monthly_fee_amount' => 500,
                    'status'             => 'active',
                    'created_by'         => $admin->id,
                ]
            );
        }

        $this->command->info('Seeding complete!');
        $this->command->info('Admin: admin@unityclub.local / admin123');
        $this->command->info('Treasurer: treasurer@unityclub.local / treasurer123');
        $this->command->info('Sample members: rahim@example.com (etc.) / member123');
    }
}
