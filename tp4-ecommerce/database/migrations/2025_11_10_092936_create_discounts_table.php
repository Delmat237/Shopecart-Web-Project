<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            
            // Informations de base
            $table->string('name'); // Nom de la remise (ex: "Soldes d'été")
            $table->text('description')->nullable(); // Description détaillée
            
            // Type et valeur de la remise
            $table->enum('type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('value', 10, 2); // 20 (pour 20%) ou 50.00 (pour 50€)
            
            // Période de validité
            $table->dateTime('start_date'); // Date de début
            $table->dateTime('end_date'); // Date de fin
            
            // Statut et priorité
            $table->boolean('is_active')->default(true); // Actif/Inactif
            $table->integer('priority')->default(0); // Pour gérer plusieurs remises (la plus haute gagne)
            
            // Conditions d'application
            $table->boolean('apply_to_all_products')->default(false); // S'applique à tous les produits ?
            $table->decimal('min_purchase_amount', 10, 2)->nullable(); // Montant minimum d'achat
            
            // Limites d'utilisation
            $table->integer('max_usage')->nullable(); // Nombre max d'utilisations totales
            $table->integer('current_usage')->default(0); // Compteur d'utilisations actuelles
            
            $table->timestamps();
            $table->softDeletes(); // Suppression douce
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};