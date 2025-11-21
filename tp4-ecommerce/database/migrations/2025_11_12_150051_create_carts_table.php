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
        Schema::create('carts', function (Blueprint $table) {
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091251_create_carts_table.php
            $table->id();
            $table->timestamps();
           
            $table->foreignId("userId")
                  ->references("id")
                  ->on("users")
                  ->onDelete("cascade");
        });
========
=======
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    $table->id();
    $table->string('session_id')->nullable();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->decimal('total', 10, 2)->default(0);
    $table->integer('items_count')->default(0);
    $table->timestamps();
    
    $table->index(['session_id']);
    $table->index(['user_id']);
});
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_150051_create_carts_table.php
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
        Schema::dropIfExists('carts');
    }
};
