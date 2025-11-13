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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 10, 2); // Montant total avec frais de port
            $table->decimal('discount_amount', 10, 2)->default(0); // Remise appliquée
            $table->decimal('shipping_cost', 10, 2)->default(0); // Frais de port
            $table->decimal('subtotal', 10, 2); // Sous-total (avant remise et frais)
            $table->string('discount_code')->nullable(); // Code promo utilisé
            $table->enum('status', [
                'pending',
                'paid',
                'preparing',
                'shipped',
                'delivered',
                'canceled'
            ])->default('pending');
            $table->text('shipping_address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
