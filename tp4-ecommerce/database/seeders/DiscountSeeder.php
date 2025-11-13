<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;
use App\Models\DiscountCodes;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Remise générale -20%
        $discount1 = Discount::create([
            'name' => 'Soldes d\'été 2024',
            'description' => '20% sur tout le site',
            'type' => 'percentage',
            'value' => 20.00,
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(20),
            'is_active' => true,
            'priority' => 1,
            'apply_to_all_products' => true,
        ]);

        DiscountCodes::create([
            'discount_id' => $discount1->id,
            'code' => 'ETE2024',
            'max_uses' => 100,
            'max_uses_per_user' => 1,
            'is_active' => true,
        ]);

        // 2. Remise bienvenue 10000FCFA
        $discount2 = Discount::create([
            'name' => 'Bienvenue - 10000 FCFA offerts',
            'description' => '10000 FCFA dès 100000 FCFA d\'achat',
            'type' => 'fixed_amount',
            'value' => 10000.00,
            'start_date' => Carbon::now()->subDays(30),
            'end_date' => Carbon::now()->addDays(60),
            'is_active' => true,
            'priority' => 2,
            'apply_to_all_products' => true,
            'min_purchase_amount' => 100000.00,
        ]);

        DiscountCodes::create([
            'discount_id' => $discount2->id,
            'code' => 'BIENVENUE10',
            'max_uses' => 500,
            'max_uses_per_user' => 1,
            'is_active' => true,
        ]);

        // 3. Black Friday -50%
        $discount3 = Discount::create([
            'name' => 'Black Friday -50%',
            'description' => '50% sur tout !',
            'type' => 'percentage',
            'value' => 50.00,
            'start_date' => Carbon::now()->addDays(5),
            'end_date' => Carbon::now()->addDays(7),
            'is_active' => true,
            'priority' => 10,
            'apply_to_all_products' => true,
        ]);

        DiscountCodes::create([
            'discount_id' => $discount3->id,
            'code' => 'BLACKFRIDAY50',
            'max_uses' => 1000,
            'max_uses_per_user' => 1,
            'is_active' => true,
        ]);

        $this->command->info('✅ 3 remises et codes créés !');
    }
}