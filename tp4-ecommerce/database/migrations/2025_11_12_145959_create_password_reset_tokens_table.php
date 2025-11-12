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
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091250_create_shelves_table.php
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
          
            $table->text("description");
            $table->foreignId("userId")
                ->references("id")
                ->on("users");
        });
========
        Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_145959_create_password_reset_tokens_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091250_create_shelves_table.php
        Schema::dropIfExists('shelves');
========
        Schema::dropIfExists('password_reset_tokens');
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_145959_create_password_reset_tokens_table.php
    }
};
