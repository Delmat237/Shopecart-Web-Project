<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
<<<<<<< HEAD
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091255_create_cart_items_table.php
            $table->id();
            $table->timestamps();
          
            $table->integer("quantity");
            $table->foreignId("cartId")
                ->references("id")
                ->on("carts")
                ->onDelete("cascade");
            $table->foreignId("productVariantId")
                ->references("id")
                ->on("product_variants");
            
        });
========
=======
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
    $table->id();
    $table->foreignId('cart_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->integer('quantity')->default(1);
    $table->decimal('unit_price', 10, 2);
    $table->decimal('total', 10, 2);
    $table->json('options')->nullable();
    $table->timestamps();
    
    $table->unique(['cart_id', 'product_id']);
});
<<<<<<< HEAD
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_150100_create_cart_items_table.php
=======
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
