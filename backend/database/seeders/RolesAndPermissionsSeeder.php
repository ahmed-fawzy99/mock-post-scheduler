<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view-users',
            'view-own-data',
            'create-users',
            'edit-users',
            'edit-own-data',
            'delete-users',
            'delete-own-data',

            // Platforms
            'create-platforms',
            'edit-platforms',
            'delete-platforms',

            // Posts
            'view-posts',
            'view-own-posts',
            'create-posts',
            'edit-posts',
            'edit-own-posts',
            'delete-posts',
            'delete-own-posts',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'user' => [
                'view-own-data',
                'edit-own-data',
                'delete-own-data',
                'view-own-posts',
                'create-posts',
                'edit-own-posts',
                'delete-own-posts',
            ],

            'admin' => $permissions // Admin gets all permissions
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName]);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
