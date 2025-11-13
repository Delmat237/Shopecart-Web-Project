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
    $table->string('order_number')->unique();
    $table->string('status')->default('pending');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('shipping', 10, 2)->default(0);
    $table->decimal('tax', 10, 2)->default(0);
    $table->decimal('discount', 10, 2)->default(0);
    $table->decimal('total', 10, 2);
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('customer_email');
    $table->string('customer_first_name');
    $table->string('customer_last_name');
    $table->string('customer_phone')->nullable();
    $table->string('shipping_address');
    $table->string('shipping_city');
    $table->string('shipping_zipcode');
    $table->string('shipping_country');
    $table->string('billing_address')->nullable();
    $table->string('billing_city')->nullable();
    $table->string('billing_zipcode')->nullable();
    $table->string('billing_country')->nullable();
    $table->string('payment_method')->nullable();
    $table->string('payment_status')->default('pending');
    $table->string('transaction_id')->nullable();
    $table->string('shipping_method')->nullable();
    $table->text('notes')->nullable();
    $table->timestamp('processed_at')->nullable();
    $table->timestamp('completed_at')->nullable();
    $table->timestamp('cancelled_at')->nullable();
    $table->timestamps();
    
    $table->index(['order_number']);
    $table->index(['status']);
    $table->index(['user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
