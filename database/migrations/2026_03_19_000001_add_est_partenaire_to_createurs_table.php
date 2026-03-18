<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permet de forcer un créateur en "Unions" (false) ou de suivre l'équipe (true/null).
     * Si est_partenaire = false → toujours Unions, jamais dans Match partenaire.
     */
    public function up(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->boolean('est_partenaire')->nullable()->after('equipe_id');
        });
    }

    public function down(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->dropColumn('est_partenaire');
        });
    }
};
