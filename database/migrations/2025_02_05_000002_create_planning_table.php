<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type', 64)->default('match_off'); // match_off, match_anniversaire, match_depannage, match_tournoi, match_agence
            $table->string('raison')->nullable();
            $table->string('createur_adverse')->nullable();
            $table->string('createur_adverse_numero')->nullable();
            $table->string('createur_adverse_at')->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['createur_id', 'date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning');
    }
};
