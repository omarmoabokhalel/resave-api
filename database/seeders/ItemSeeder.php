<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::insert([
            [
                'name' => 'Plastic',
                'description' => 'Plastic bottles and containers',
                'pricing_type' => 'kg',
                'price' => 6,
            ],
            [
                'name' => 'Paper',
                'description' => 'Paper & cardboard',
                'pricing_type' => 'kg',
                'price' => 3,
            ],
            [
                'name' => 'Metal',
                'description' => 'Aluminum & metal waste',
                'pricing_type' => 'kg',
                'price' => 10,
            ],
            [
                'name' => 'Old Fridge',
                'description' => 'Large electronic item',
                'pricing_type' => 'piece',
                'price' => 150,
            ],
        ]);
    }
}
