<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        Schema::table('score_fidelite', function (Blueprint $table) use ($now) {
            $table->unsignedSmallInteger('annee')->default($now->year)->after('createur_id');
            $table->unsignedTinyInteger('mois')->default($now->month)->after('annee');
        });

        Schema::table('score_fidelite', function (Blueprint $table) {
            $table->dropUnique(['createur_id']);
            $table->unique(['createur_id', 'annee', 'mois']);
        });
    }

    public function down(): void
    {
        Schema::table('score_fidelite', function (Blueprint $table) {
            $table->dropUnique(['createur_id', 'annee', 'mois']);
        });
        Schema::table('score_fidelite', function (Blueprint $table) {
            $table->dropColumn(['annee', 'mois']);
            $table->unique('createur_id');
        });
    }
};
