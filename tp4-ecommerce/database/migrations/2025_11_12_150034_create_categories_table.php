<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
<<<<<<< HEAD
<<<<<<< HEAD

=======
use App\Enums\PaymentStatus;
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======

>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
<<<<<<< HEAD
<<<<<<< HEAD
        Schema::create('categories', function (Blueprint $table) {
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091252_create_categories_table.php
            $table->id();
            $table->timestamps();
            $table->text("description");
            
            $table->foreignId("shelveId")
            ->reference("id")
            ->on("shelve")
            ->onDelete('cascade');

        });
========
=======
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_093031_create_payments_table.php
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum("status",array_column(PaymentStatus::cases(),"value"))->default(PaymentStatus::PENDING);
            $table->string("paymentMethod");
            $table->string("transactionId");
        });
========
        Schema::create('categories', function (Blueprint $table) {
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
        Schema::create('categories', function (Blueprint $table) {
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
    $table->integer('position')->default(0);
    $table->boolean('is_visible')->default(true);
    $table->timestamps();
    
    $table->index(['is_visible', 'position']);
});
<<<<<<< HEAD
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_150034_create_categories_table.php
=======
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<< HEAD
<<<<<<< HEAD
        Schema::dropIfExists('categories');
=======
        Schema::dropIfExists('payments');
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
        Schema::dropIfExists('categories');
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    }
};
