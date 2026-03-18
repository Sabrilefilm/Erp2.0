<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recompenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained()->cascadeOnDelete();
            $table->string('type', 64); // TikTok, PayPal, Carte cadeau
            $table->decimal('montant', 12, 2)->default(0);
            $table->string('raison')->nullable();
            $table->foreignId('attribue_par')->nullable()->constrained('users')->nullOnDelete();
            $table->string('statut', 32)->default('attribue');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recompenses');
    }
};
