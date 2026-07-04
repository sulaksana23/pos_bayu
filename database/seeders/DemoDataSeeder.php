<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users
        User::firstOrCreate(
            ['email' => 'admin@pos.test'],
            [
                'name' => 'Admin Demo',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@pos.test'],
            [
                'name' => 'Manager Demo',
                'role' => 'manager',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'cashier@pos.test'],
            [
                'name' => 'Kasir Demo',
                'role' => 'cashier',
                'password' => Hash::make('password'),
            ]
        );

        // Create categories
        $categories = [
            ['name' => 'Makanan', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Minuman', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Snack', 'sort_order' => 3, 'is_active' => true],
            ['name' => 'Rokok', 'sort_order' => 4, 'is_active' => true],
            ['name' => 'Sembako', 'sort_order' => 5, 'is_active' => true],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // Get category IDs
        $makanan = Category::where('name', 'Makanan')->first();
        $minuman = Category::where('name', 'Minuman')->first();
        $snack = Category::where('name', 'Snack')->first();
        $rokok = Category::where('name', 'Rokok')->first();
        $sembako = Category::where('name', 'Sembako')->first();

        // Create products
        $products = [
            // Makanan
            ['category_id' => $makanan->id, 'name' => 'Nasi Goreng', 'sku' => 'FD-001', 'barcode' => '8991234501001', 'price' => 15000, 'cost' => 8000, 'stock' => 50, 'min_stock' => 5, 'unit' => 'porsi'],
            ['category_id' => $makanan->id, 'name' => 'Mie Goreng', 'sku' => 'FD-002', 'barcode' => '8991234501002', 'price' => 12000, 'cost' => 6000, 'stock' => 45, 'min_stock' => 5, 'unit' => 'porsi'],
            ['category_id' => $makanan->id, 'name' => 'Ayam Goreng', 'sku' => 'FD-003', 'barcode' => '8991234501003', 'price' => 18000, 'cost' => 10000, 'stock' => 30, 'min_stock' => 5, 'unit' => 'porsi'],
            ['category_id' => $makanan->id, 'name' => 'Soto Ayam', 'sku' => 'FD-004', 'barcode' => '8991234501004', 'price' => 14000, 'cost' => 7000, 'stock' => 35, 'min_stock' => 5, 'unit' => 'porsi'],
            
            // Minuman
            ['category_id' => $minuman->id, 'name' => 'Es Teh Manis', 'sku' => 'DRK-001', 'barcode' => '8991234502001', 'price' => 5000, 'cost' => 2000, 'stock' => 100, 'min_stock' => 10, 'unit' => 'gelas'],
            ['category_id' => $minuman->id, 'name' => 'Es Jeruk', 'sku' => 'DRK-002', 'barcode' => '8991234502002', 'price' => 7000, 'cost' => 3000, 'stock' => 80, 'min_stock' => 10, 'unit' => 'gelas'],
            ['category_id' => $minuman->id, 'name' => 'Kopi Susu', 'sku' => 'DRK-003', 'barcode' => '8991234502003', 'price' => 10000, 'cost' => 4000, 'stock' => 60, 'min_stock' => 10, 'unit' => 'gelas'],
            ['category_id' => $minuman->id, 'name' => 'Air Mineral Botol', 'sku' => 'DRK-004', 'barcode' => '8991234502004', 'price' => 3000, 'cost' => 1500, 'stock' => 150, 'min_stock' => 20, 'unit' => 'botol'],
            
            // Snack
            ['category_id' => $snack->id, 'name' => 'Chitato', 'sku' => 'SNK-001', 'barcode' => '8991234503001', 'price' => 10000, 'cost' => 6000, 'stock' => 75, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => $snack->id, 'name' => 'Oreo', 'sku' => 'SNK-002', 'barcode' => '8991234503002', 'price' => 8000, 'cost' => 5000, 'stock' => 90, 'min_stock' => 10, 'unit' => 'pcs'],
            ['category_id' => $snack->id, 'name' => 'Biskuit Roma', 'sku' => 'SNK-003', 'barcode' => '8991234503003', 'price' => 6000, 'cost' => 3500, 'stock' => 120, 'min_stock' => 15, 'unit' => 'pcs'],
            ['category_id' => $snack->id, 'name' => 'Wafer Tango', 'sku' => 'SNK-004', 'barcode' => '8991234503004', 'price' => 5000, 'cost' => 3000, 'stock' => 100, 'min_stock' => 15, 'unit' => 'pcs'],
            
            // Rokok
            ['category_id' => $rokok->id, 'name' => 'Gudang Garam Merah', 'sku' => 'CIG-001', 'barcode' => '8991234504001', 'price' => 22000, 'cost' => 20000, 'stock' => 40, 'min_stock' => 5, 'unit' => 'bungkus'],
            ['category_id' => $rokok->id, 'name' => 'Sampoerna Mild', 'sku' => 'CIG-002', 'barcode' => '8991234504002', 'price' => 25000, 'cost' => 23000, 'stock' => 35, 'min_stock' => 5, 'unit' => 'bungkus'],
            ['category_id' => $rokok->id, 'name' => 'Marlboro', 'sku' => 'CIG-003', 'barcode' => '8991234504003', 'price' => 28000, 'cost' => 26000, 'stock' => 30, 'min_stock' => 5, 'unit' => 'bungkus'],
            
            // Sembako
            ['category_id' => $sembako->id, 'name' => 'Beras Premium 5kg', 'sku' => 'GRC-001', 'barcode' => '8991234505001', 'price' => 75000, 'cost' => 65000, 'stock' => 25, 'min_stock' => 3, 'unit' => 'kg'],
            ['category_id' => $sembako->id, 'name' => 'Minyak Goreng 2L', 'sku' => 'GRC-002', 'barcode' => '8991234505002', 'price' => 35000, 'cost' => 30000, 'stock' => 40, 'min_stock' => 5, 'unit' => 'liter'],
            ['category_id' => $sembako->id, 'name' => 'Gula Pasir 1kg', 'sku' => 'GRC-003', 'barcode' => '8991234505003', 'price' => 15000, 'cost' => 12000, 'stock' => 60, 'min_stock' => 10, 'unit' => 'kg'],
            ['category_id' => $sembako->id, 'name' => 'Telur Ayam 1kg', 'sku' => 'GRC-004', 'barcode' => '8991234505004', 'price' => 28000, 'cost' => 25000, 'stock' => 50, 'min_stock' => 10, 'unit' => 'kg'],
            ['category_id' => $sembako->id, 'name' => 'Indomie Goreng', 'sku' => 'GRC-005', 'barcode' => '8991234505005', 'price' => 3500, 'cost' => 2500, 'stock' => 200, 'min_stock' => 30, 'unit' => 'pcs'],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['sku' => $product['sku']],
                array_merge($product, ['is_active' => true])
            );
        }

        // Create customers
        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '081234567890', 'email' => 'budi@example.com', 'address' => 'Jl. Merdeka No. 10, Jakarta'],
            ['name' => 'Siti Nurhaliza', 'phone' => '081234567891', 'email' => 'siti@example.com', 'address' => 'Jl. Sudirman No. 25, Jakarta'],
            ['name' => 'Andi Wijaya', 'phone' => '081234567892', 'email' => 'andi@example.com', 'address' => 'Jl. Gatot Subroto No. 15, Jakarta'],
            ['name' => 'Dewi Lestari', 'phone' => '081234567893', 'email' => 'dewi@example.com', 'address' => 'Jl. Thamrin No. 5, Jakarta'],
            ['name' => 'Rudi Hermawan', 'phone' => '081234567894', 'email' => 'rudi@example.com', 'address' => 'Jl. Kuningan No. 8, Jakarta'],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['phone' => $customer['phone']],
                array_merge($customer, ['is_active' => true, 'points' => 0, 'total_spent' => 0, 'visit_count' => 0])
            );
        }

        $this->command->info('Demo data seeded successfully!');
    }
}
