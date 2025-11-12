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
>>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c:tp4-ecommerce/database/migrations/2025_11_12_150034_create_categories_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
