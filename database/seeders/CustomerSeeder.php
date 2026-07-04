<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name'=>'Pelanggan Umum',    'phone'=>null,           'email'=>null],
            ['name'=>'Budi Santoso',      'phone'=>'081234567001', 'email'=>'budi.santoso@gmail.com'],
            ['name'=>'Siti Rahayu',       'phone'=>'081234567002', 'email'=>'siti.rahayu@gmail.com'],
            ['name'=>'Ahmad Dahlan',      'phone'=>'081234567003', 'email'=>'ahmad.dahlan@gmail.com'],
            ['name'=>'Dewi Lestari',      'phone'=>'081234567004', 'email'=>'dewi.lestari@gmail.com'],
            ['name'=>'Rudi Hartono',      'phone'=>'081234567005', 'email'=>'rudi.hartono@gmail.com'],
            ['name'=>'Ani Wulandari',     'phone'=>'081234567006', 'email'=>'ani.wulandari@gmail.com'],
            ['name'=>'Hendra Gunawan',    'phone'=>'081234567007', 'email'=>'hendra.gunawan@gmail.com'],
            ['name'=>'Maya Kusuma',       'phone'=>'081234567008', 'email'=>'maya.kusuma@gmail.com'],
            ['name'=>'Eko Prasetyo',      'phone'=>'081234567009', 'email'=>'eko.prasetyo@gmail.com'],
            ['name'=>'Linda Wijaya',      'phone'=>'081234567010', 'email'=>'linda.wijaya@gmail.com'],
            ['name'=>'Agus Setiawan',     'phone'=>'081234567011', 'email'=>'agus.setiawan@gmail.com'],
            ['name'=>'Rina Puspita',      'phone'=>'081234567012', 'email'=>'rina.puspita@gmail.com'],
            ['name'=>'Bambang Sugiarto',  'phone'=>'081234567013', 'email'=>'bambang.sugiarto@gmail.com'],
            ['name'=>'Fitri Handayani',   'phone'=>'081234567014', 'email'=>'fitri.handayani@gmail.com'],
        ];
        foreach ($rows as $c) {
            Customer::updateOrCreate(
                ['name' => $c['name']],
                array_merge($c, ['is_active' => true, 'points' => 0, 'total_spent' => 0, 'visit_count' => 0])
            );
        }
    }
}
