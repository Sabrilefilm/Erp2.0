<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->decimal('heures_mois', 6, 2)->nullable()->change();
        });

        Schema::table('createur_stats_mensuelles', function (Blueprint $table) {
            $table->decimal('heures_stream', 6, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->unsignedInteger('heures_mois')->nullable()->change();
        });

        Schema::table('createur_stats_mensuelles', function (Blueprint $table) {
            $table->unsignedInteger('heures_stream')->nullable()->change();
        });
    }
};
