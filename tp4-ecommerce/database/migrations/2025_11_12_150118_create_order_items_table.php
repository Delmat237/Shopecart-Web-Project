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
        Schema::create('order_items', function (Blueprint $table) {
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_093111_create_order_items_table.php
            $table->id();
            $table->timestamps();
            
            $table->string("quantity");
            
            $table->foreignId("orderId")
                ->references("id")
                ->on("orders");
            $table->foreignId("productVariantId")
                ->references("id")
                ->on("product_variants");
        });
========
=======
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->integer('quantity');
    $table->decimal('unit_price', 10, 2);
    $table->decimal('total', 10, 2);
    $table->string('product_name');
    $table->string('product_sku')->nullable();
    $table->json('options')->nullable();
    $table->timestamps();
});
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_150118_create_order_items_table.php
=======
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
