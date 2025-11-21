<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Roles;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD:tp4-ecommerce/database/migrations/2014_10_12_000000_create_users_table.php
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->enum("role",array_column(Roles::cases(),"value"))->default(Roles::USER->value);
        });
=======
=======
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
=======
>>>>>>> 4cb05ea554260cd90d6ada91d316a864b9978857
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_145948_create_users_table.php
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
        Schema::dropIfExists('users');
    }
};
