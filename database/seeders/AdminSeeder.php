<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat permissions
        $permissions = [
            'manage_products', 'manage_categories', 'manage_customers',
            'manage_shifts', 'manage_transactions', 'manage_reports',
            'manage_users', 'manage_inventory', 'manage_accounting',
            'view_dashboard', 'view_reports',
        ];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Buat roles
        $superadmin = Role::firstOrCreate(['name' => 'superadministrator', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

        $superadmin->syncPermissions(Permission::all());
        $admin->syncPermissions(Permission::all());
        $manager->syncPermissions(['manage_products', 'manage_categories', 'manage_inventory', 'view_dashboard', 'view_reports', 'manage_shifts', 'manage_transactions']);
        $cashier->syncPermissions(['view_dashboard', 'manage_transactions', 'manage_shifts']);

        $admins = [
            ['name' => 'Admin GHouse',       'email' => 'admin@balitechsolution.com', 'role' => 'admin',              'pin' => '0000'],
            ['name' => 'Manajer Toko',       'email' => 'manager@balitechsolution.com','role' => 'manager',            'pin' => '1111'],
            ['name' => 'Kasir 1',            'email' => 'kasir1@balitechsolution.com', 'role' => 'cashier',            'pin' => '1234'],
            ['name' => 'Kasir 2',            'email' => 'kasir2@balitechsolution.com', 'role' => 'cashier',            'pin' => '5678'],
            ['name' => 'Sulaksana',          'email' => 'sulaksana60@gmail.com',        'role' => 'superadministrator', 'pin' => '9999'],
        ];
        foreach ($admins as $a) {
            $user = User::updateOrCreate(
                ['email' => $a['email']],
                array_merge($a, [
                    'password' => Hash::make($a['email'] === 'sulaksana60@gmail.com' ? 'Superman2000@' : 'password'),
                    'phone' => '+6281234567001',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );
            // Assign Spatie role
            $roleName = $a['role'];
            if (in_array($roleName, ['superadministrator', 'admin', 'manager', 'cashier'])) {
                $user->assignRole($roleName);
            }
        }
    }
}
