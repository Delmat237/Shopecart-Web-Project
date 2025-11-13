<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Créer des catégories
        $telephone = Category::create(['name' => 'Téléphones', 'slug' => 'telephones']);
        $camera = Category::create(['name' => 'Caméras', 'slug' => 'cameras']);

        // Produits de test
        $products = [
            ['name' => 'Asus ROG Phone 9 Pro', 'price' => 850000, 'stock' => 50, 'category_id' => $telephone->id],
            ['name' => 'Alcatel 1', 'price' => 38000, 'stock' => 30, 'category_id' => $telephone->id],
            ['name' => 'Google Pixel 9', 'price' => 750000, 'stock' => 200, 'category_id' => $telephone->id],
            ['name' => 'Leica M11 P', 'price' => 9850000, 'stock' => 100, 'category_id' => $camera->id],
            ['name' => 'Polaroid Now Instant Gen II', 'price' => 145000, 'stock' => 75, 'category_id' => $camera->id],
            ['name' => 'Sony Alpha 6700', 'price' => 1250000, 'stock' => 150, 'category_id' => $camera->id],
        ];

        foreach ($products as $data) {
            Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => 'Description de ' . $data['name'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'category_id' => $data['category_id'],
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'is_visible' => true,
                'is_featured' => rand(0, 1) === 1,
                'published_at' => now(),
            ]);
        }

        $this->command->info('✅ 6 produits créés !');
    }
}