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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // --- MISSING COLUMNS START HERE ---
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable(); // Assuming products link to categories
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock');

            // Columns needed for the failing query:
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            
            // Soft Deletes (needed for 'products.deleted_at' in the query)
            $table->softDeletes(); 
            // --- MISSING COLUMNS END HERE ---

            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};