<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('createur_stats_mensuelles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained('createurs')->cascadeOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->unsignedTinyInteger('mois'); // 1-12
            $table->unsignedInteger('jours_stream')->nullable();
            $table->unsignedInteger('heures_stream')->nullable();
            $table->unsignedInteger('diamants')->nullable();
            $table->timestamps();

            $table->unique(['createur_id', 'annee', 'mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('createur_stats_mensuelles');
    }
};
