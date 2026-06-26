<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Applications
            'view-applications',
            'approve-applications',
            'reject-applications',

            // Members
            'view-members',
            'edit-members',
            'deactivate-members',

            // Collections
            'view-collections',
            'manage-collections',

            // Payment approvals
            'view-payments',
            'approve-payments',
            'reject-payments',

            // Finance
            'view-expenses',
            'manage-expenses',
            'view-income',
            'manage-income',
            'view-fdr',
            'manage-fdr',

            // Content
            'manage-notices',
            'manage-meeting-minutes',

            // Reports
            'view-reports',

            // User management
            'manage-users',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Member: basic portal access only (no admin permissions)
        $member = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

        // Treasurer: finance-focused
        $treasurer = Role::firstOrCreate(['name' => 'treasurer', 'guard_name' => 'web']);
        $treasurer->syncPermissions([
            'view-applications',
            'view-members',
            'view-collections',
            'manage-collections',
            'view-payments',
            'approve-payments',
            'reject-payments',
            'view-expenses',
            'manage-expenses',
            'view-income',
            'manage-income',
            'view-fdr',
            'manage-fdr',
            'view-reports',
        ]);

        // Admin: full access except user management (reserved for super_admin)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view-applications',
            'approve-applications',
            'reject-applications',
            'view-members',
            'edit-members',
            'deactivate-members',
            'view-collections',
            'manage-collections',
            'view-payments',
            'approve-payments',
            'reject-payments',
            'view-expenses',
            'manage-expenses',
            'view-income',
            'manage-income',
            'view-fdr',
            'manage-fdr',
            'manage-notices',
            'manage-meeting-minutes',
            'view-reports',
            'manage-users',
        ]);

        // Super Admin: all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());
    }
}
