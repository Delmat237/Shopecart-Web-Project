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
            $table->id();
            $table->timestamps();
           
            $table->foreignId("userId")
                  ->references("id")
                  ->on("users")
                  ->onDelete("cascade");
        });
    $table->id();
    $table->string('session_id')->nullable();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->decimal('total', 10, 2)->default(0);
    $table->integer('items_count')->default(0);
    $table->timestamps();
    
    $table->index(['session_id']);
    $table->index(['user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
