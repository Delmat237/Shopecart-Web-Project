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
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091250_create_shelves_table.php
        Schema::create('shelves', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
          
            $table->text("description");
            $table->foreignId("userId")
                ->references("id")
                ->on("users");
=======
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_12_074223_add_phone_and_address_to_users_table.php
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            //
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
        });
========
=======
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
        Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
<<<<<<< HEAD
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_145959_create_password_reset_tokens_table.php
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
<<<<<<<< HEAD:tp4-ecommerce/database/migrations/2025_11_10_091250_create_shelves_table.php
        Schema::dropIfExists('shelves');
========
        Schema::dropIfExists('password_reset_tokens');
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_145959_create_password_reset_tokens_table.php
=======
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address']);
            //
        });
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
        Schema::dropIfExists('password_reset_tokens');
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    }
};
