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
            ['name'=>'Doni Prasetyo',     'phone'=>'081234567015', 'email'=>'doni.prasetyo@gmail.com'],
            ['name'=>'Sari Dewi',          'phone'=>'081234567016', 'email'=>'sari.dewi@gmail.com'],
            ['name'=>'Hendra Saputra',    'phone'=>'081234567017', 'email'=>'hendra.saputra@gmail.com'],
            ['name'=>'Rina Marlina',      'phone'=>'081234567018', 'email'=>'rina.marlina@gmail.com'],
            ['name'=>'Adi Nugroho',       'phone'=>'081234567019', 'email'=>'adi.nugroho@gmail.com'],
            ['name'=>'Nia Kurniawan',     'phone'=>'081234567020', 'email'=>'nia.kurniawan@gmail.com'],
            ['name'=>'Bayu Pratama',      'phone'=>'081234567021', 'email'=>'bayu.pratama@gmail.com'],
            ['name'=>'Tuti Alawiyah',     'phone'=>'081234567022', 'email'=>'tuti.alawiyah@gmail.com'],
            ['name'=>'Rizky Fadhilah',    'phone'=>'081234567023', 'email'=>'rizky.fadhilah@gmail.com'],
            ['name'=>'Mega Wati',         'phone'=>'081234567024', 'email'=>'mega.wati@gmail.com'],
            ['name'=>'Dede Suryana',      'phone'=>'081234567025', 'email'=>'dede.suryana@gmail.com'],
            ['name'=>'Winda Permata',     'phone'=>'081234567026', 'email'=>'winda.permata@gmail.com'],
            ['name'=>'Yogi Pratama',      'phone'=>'081234567027', 'email'=>'yogi.pratama@gmail.com'],
            ['name'=>'Indah Permatasari', 'phone'=>'081234567028', 'email'=>'indah.permatasari@gmail.com'],
            ['name'=>'Irfan Hakim',       'phone'=>'081234567029', 'email'=>'irfan.hakim@gmail.com'],
            ['name'=>'Ratna Sari Dewi',   'phone'=>'081234567030', 'email'=>'ratna.saridewi@gmail.com'],
        ];
        foreach ($rows as $c) {
            Customer::updateOrCreate(
                ['name' => $c['name']],
                array_merge($c, ['is_active' => true, 'points' => 0, 'total_spent' => 0, 'visit_count' => 0])
            );
        }
    }
}
