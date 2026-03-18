<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained()->cascadeOnDelete();
            $table->string('type', 64); // Avertissement, Blâme, Suspension, Exclusion
            $table->string('niveau', 32)->default('agence'); // agent, agence
            $table->string('raison')->nullable();
            $table->foreignId('attribue_par')->nullable()->constrained('users')->nullOnDelete();
            $table->string('statut', 32)->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};
