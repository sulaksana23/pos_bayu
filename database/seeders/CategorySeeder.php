<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name'=>'Makanan', 'slug'=>'makanan', 'color'=>'#f97316', 'icon'=>'fa-bowl-food',     'sort_order'=>1],
            ['name'=>'Minuman', 'slug'=>'minuman', 'color'=>'#3b82f6', 'icon'=>'fa-bottle-water',  'sort_order'=>2],
            ['name'=>'Snack',   'slug'=>'snack',   'color'=>'#a855f7', 'icon'=>'fa-cookie-bite',   'sort_order'=>3],
            ['name'=>'Rokok',   'slug'=>'rokok',   'color'=>'#ef4444', 'icon'=>'fa-smoking',       'sort_order'=>4],
            ['name'=>'Sembako', 'slug'=>'sembako', 'color'=>'#10b981', 'icon'=>'fa-basket-shopping','sort_order'=>5],
            ['name'=>'ATK',     'slug'=>'atk',     'color'=>'#64748b', 'icon'=>'fa-pen',           'sort_order'=>6],
            ['name'=>'Obat',    'slug'=>'obat',    'color'=>'#06b6d4', 'icon'=>'fa-pills',         'sort_order'=>7],
        ];
        foreach ($cats as $c) Category::updateOrCreate(['slug' => $c['slug']], array_merge($c, ['is_active' => true]));
    }
}
