<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_code_usages', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('discount_code_id')
                  ->constrained('discount_codes')
                  ->onDelete('cascade'); // Si le code est supprimé, supprimer l'historique
            
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null'); // Si l'user est supprimé, garder l'historique mais mettre null
            
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->onDelete('set null'); // Si la commande est supprimée, garder l'historique
            
            // Informations sur l'utilisation
            $table->decimal('discount_amount', 10, 2); // Montant économisé (ex: 25.50€)
            $table->string('ip_address')->nullable(); // IP pour éviter les abus
            
            $table->timestamps(); // created_at = quand le code a été utilisé
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_code_usages');
    }
};