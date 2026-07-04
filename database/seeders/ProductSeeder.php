<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $slug) => Category::where('slug', $slug)->value('id');
        $products = [
            // Makanan
            ['name'=>'Indomie Goreng',           'sku'=>'IDM-GRG',  'barcode'=>'8996001240123', 'category_id'=>$cat('makanan'),'price'=>3500,   'cost'=>2500,   'stock'=>120, 'min_stock'=>20, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=400'],
            ['name'=>'Indomie Kuah Soto',        'sku'=>'IDM-STO',  'barcode'=>'8996001240222', 'category_id'=>$cat('makanan'),'price'=>3500,   'cost'=>2500,   'stock'=>80,  'min_stock'=>20, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400'],
            ['name'=>'Mie Sedap Goreng',         'sku'=>'SED-GRG',  'barcode'=>'8996001303341', 'category_id'=>$cat('makanan'),'price'=>3500,   'cost'=>2400,   'stock'=>60,  'min_stock'=>15, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400'],
            ['name'=>'Roti Tawar Sari Roti',     'sku'=>'RT-SR',    'barcode'=>'8992771100019', 'category_id'=>$cat('makanan'),'price'=>13000,  'cost'=>10000,  'stock'=>48,  'min_stock'=>10, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400'],
            ['name'=>'Kacang Garuda 200g',       'sku'=>'GRD-200',  'barcode'=>'8992832100025', 'category_id'=>$cat('makanan'),'price'=>18000,  'cost'=>14000,  'stock'=>36,  'min_stock'=>10, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1599599810769-bcde5a160d32?w=400'],
            
            // Minuman
            ['name'=>'Aqua 600ml',               'sku'=>'AQ-600',   'barcode'=>'8886001100034', 'category_id'=>$cat('minuman'),'price'=>3500,   'cost'=>2200,   'stock'=>200, 'min_stock'=>30, 'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400'],
            ['name'=>'Aqua 1.5L',                'sku'=>'AQ-1500',  'barcode'=>'8886001100041', 'category_id'=>$cat('minuman'),'price'=>8000,   'cost'=>6000,   'stock'=>72,  'min_stock'=>15, 'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1625772452859-1c03d5bf1137?w=400'],
            ['name'=>'Teh Pucuk 350ml',          'sku'=>'PUC-350',  'barcode'=>'8996001600378', 'category_id'=>$cat('minuman'),'price'=>4000,   'cost'=>2800,   'stock'=>96,  'min_stock'=>20, 'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400'],
            ['name'=>'Coca Cola 390ml',          'sku'=>'CC-390',   'barcode'=>'8992752100016', 'category_id'=>$cat('minuman'),'price'=>6000,   'cost'=>4500,   'stock'=>84,  'min_stock'=>20, 'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1554866585-cd94860890b7?w=400'],
            ['name'=>'Kopi Kapal Api',           'sku'=>'KAP-30G',  'barcode'=>'8991002100034', 'category_id'=>$cat('minuman'),'price'=>2500,   'cost'=>1700,   'stock'=>150, 'min_stock'=>30, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400'],
            ['name'=>'Kopi Good Day',            'sku'=>'GD-25',    'barcode'=>'8991002105433', 'category_id'=>$cat('minuman'),'price'=>2500,   'cost'=>1700,   'stock'=>90,  'min_stock'=>20, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400'],
            ['name'=>'Susu Ultra Milk 200ml',    'sku'=>'ULT-200',  'barcode'=>'8992753100013', 'category_id'=>$cat('minuman'),'price'=>7000,   'cost'=>5500,   'stock'=>72,  'min_stock'=>15, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=400'],
            
            // Snack
            ['name'=>'Chitato 68g',              'sku'=>'CHT-68',   'barcode'=>'8996001302026', 'category_id'=>$cat('snack'),  'price'=>12000,  'cost'=>9000,   'stock'=>36,  'min_stock'=>10, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400'],
            ['name'=>'Tango Wafer',              'sku'=>'TG-WFR',   'barcode'=>'8996001300244', 'category_id'=>$cat('snack'),  'price'=>6500,   'cost'=>4500,   'stock'=>48,  'min_stock'=>12, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=400'],
            ['name'=>'Oreo 137g',                'sku'=>'ORO-137',  'barcode'=>'8991106100018', 'category_id'=>$cat('snack'),  'price'=>14000,  'cost'=>11000,  'stock'=>48,  'min_stock'=>12, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?w=400'],
            ['name'=>'Beng Beng',                'sku'=>'BB-20',    'barcode'=>'8992741100014', 'category_id'=>$cat('snack'),  'price'=>2500,   'cost'=>1800,   'stock'=>120, 'min_stock'=>25, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1511381939415-e44015466834?w=400'],
            
            // Rokok
            ['name'=>'Sampoerna Mild 16',        'sku'=>'SPM-16',   'barcode'=>'8999909000018', 'category_id'=>$cat('rokok'),  'price'=>25000,  'cost'=>22000,  'stock'=>90,  'min_stock'=>15, 'unit'=>'pack', 'image_url'=>'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400'],
            ['name'=>'Gudang Garam Surya 16',    'sku'=>'GG-16',    'barcode'=>'8999909010017', 'category_id'=>$cat('rokok'),  'price'=>22000,  'cost'=>19000,  'stock'=>60,  'min_stock'=>15, 'unit'=>'pack', 'image_url'=>'https://images.unsplash.com/photo-1605900055849-93b14f971862?w=400'],
            ['name'=>'Marlboro Merah',           'sku'=>'MRL-RED',  'barcode'=>'8999909020016', 'category_id'=>$cat('rokok'),  'price'=>30000,  'cost'=>26000,  'stock'=>48,  'min_stock'=>12, 'unit'=>'pack', 'image_url'=>'https://images.unsplash.com/photo-1598475472133-ee7675d6d6d3?w=400'],
            
            // Sembako
            ['name'=>'Beras 5kg',                'sku'=>'BRS-5',    'barcode'=>'8991234500005', 'category_id'=>$cat('sembako'),'price'=>70000,  'cost'=>62000,  'stock'=>24,  'min_stock'=>6,  'unit'=>'kg', 'image_url'=>'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400'],
            ['name'=>'Minyak Goreng 1L',         'sku'=>'MG-1L',    'barcode'=>'8991234500012', 'category_id'=>$cat('sembako'),'price'=>18000,  'cost'=>15000,  'stock'=>48,  'min_stock'=>10, 'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400'],
            ['name'=>'Gula Pasir 1kg',           'sku'=>'GLP-1',    'barcode'=>'8991234500029', 'category_id'=>$cat('sembako'),'price'=>16000,  'cost'=>13000,  'stock'=>36,  'min_stock'=>10, 'unit'=>'kg', 'image_url'=>'https://images.unsplash.com/photo-1518110925495-5816302e46bf?w=400'],
            ['name'=>'Telur 1kg',                'sku'=>'TLR-1',    'barcode'=>'8991234500036', 'category_id'=>$cat('sembako'),'price'=>23000,  'cost'=>19000,  'stock'=>24,  'min_stock'=>6,  'unit'=>'kg', 'image_url'=>'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400'],
            ['name'=>'Tepung Terigu 1kg',        'sku'=>'TPG-1',    'barcode'=>'8991234500043', 'category_id'=>$cat('sembako'),'price'=>12000,  'cost'=>9500,   'stock'=>36,  'min_stock'=>10, 'unit'=>'kg', 'image_url'=>'https://images.unsplash.com/photo-1628088062854-d1870b4553da?w=400'],
            
            // ATK
            ['name'=>'Pulpen Pilot',             'sku'=>'PL-PLT',   'barcode'=>'4901688101013', 'category_id'=>$cat('atk'),    'price'=>5000,   'cost'=>3500,   'stock'=>80,  'min_stock'=>15, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1586075010923-2dd4570fb338?w=400'],
            ['name'=>'Buku Tulis 38 Lembar',     'sku'=>'BK-38',    'barcode'=>'8993988100012', 'category_id'=>$cat('atk'),    'price'=>6500,   'cost'=>4500,   'stock'=>48,  'min_stock'=>10, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400'],
            ['name'=>'Penggaris 30cm',           'sku'=>'PGR-30',   'barcode'=>'8993988100029', 'category_id'=>$cat('atk'),    'price'=>3500,   'cost'=>2500,   'stock'=>60,  'min_stock'=>15, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1596464716127-f2a82984de30?w=400'],
            ['name'=>'Pensil 2B',                'sku'=>'PNS-2B',   'barcode'=>'8993988100036', 'category_id'=>$cat('atk'),    'price'=>2500,   'cost'=>1800,   'stock'=>96,  'min_stock'=>20, 'unit'=>'pcs', 'image_url'=>'https://images.unsplash.com/photo-1594642632076-91bbf27b50a6?w=400'],
            
            // Obat
            ['name'=>'Paracetamol Strip',        'sku'=>'PCT-STR',  'barcode'=>'8996007100012', 'category_id'=>$cat('obat'),   'price'=>5000,   'cost'=>3000,   'stock'=>60,  'min_stock'=>15, 'unit'=>'pack', 'image_url'=>'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=400'],
            ['name'=>'Betadine 30ml',            'sku'=>'BTD-30',   'barcode'=>'8996007100029', 'category_id'=>$cat('obat'),   'price'=>28000,  'cost'=>22000,  'stock'=>18,  'min_stock'=>5,  'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400'],
            ['name'=>'OBH Combi Sirup',          'sku'=>'OBH-SRP',  'barcode'=>'8996007100036', 'category_id'=>$cat('obat'),   'price'=>22000,  'cost'=>18000,  'stock'=>24,  'min_stock'=>6,  'unit'=>'btl', 'image_url'=>'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=400'],
            ['name'=>'Hansaplast Strip 10s',     'sku'=>'HNS-10',   'barcode'=>'8996007100043', 'category_id'=>$cat('obat'),   'price'=>12000,  'cost'=>9000,   'stock'=>48,  'min_stock'=>12, 'unit'=>'box', 'image_url'=>'https://images.unsplash.com/photo-1603398938378-e54eab446dde?w=400'],
        ];
        foreach ($products as $p) {
            Product::updateOrCreate(
                ['sku' => $p['sku']], 
                array_merge($p, ['is_active' => true, 'description' => null])
            );
        }
    }
}
