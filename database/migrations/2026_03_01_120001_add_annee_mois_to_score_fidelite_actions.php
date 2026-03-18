<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        Schema::table('score_fidelite_actions', function (Blueprint $table) use ($now) {
            $table->unsignedSmallInteger('annee')->default($now->year)->after('createur_id');
            $table->unsignedTinyInteger('mois')->default($now->month)->after('annee');
        });
    }

    public function down(): void
    {
        Schema::table('score_fidelite_actions', function (Blueprint $table) {
            $table->dropColumn(['annee', 'mois']);
        });
    }
};
