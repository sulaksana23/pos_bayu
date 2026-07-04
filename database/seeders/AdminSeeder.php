<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            ['name' => 'Admin GHouse',       'email' => 'admin@balitechsolution.com', 'role' => 'admin',   'pin' => '0000'],
            ['name' => 'Manajer Toko',       'email' => 'manager@balitechsolution.com','role' => 'manager', 'pin' => '1111'],
            ['name' => 'Kasir 1',            'email' => 'kasir1@balitechsolution.com', 'role' => 'cashier', 'pin' => '1234'],
            ['name' => 'Kasir 2',            'email' => 'kasir2@balitechsolution.com', 'role' => 'cashier', 'pin' => '5678'],
        ];
        foreach ($admins as $a) {
            User::updateOrCreate(
                ['email' => $a['email']],
                array_merge($a, [
                    'password' => Hash::make('password'),
                    'phone' => '+6281234567001',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
