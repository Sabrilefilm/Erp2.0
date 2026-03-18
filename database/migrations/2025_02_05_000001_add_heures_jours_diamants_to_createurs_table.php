<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->unsignedInteger('heures_mois')->nullable()->after('stats_engagement');
            $table->unsignedInteger('jours_mois')->nullable()->after('heures_mois');
            $table->unsignedInteger('diamants')->nullable()->after('jours_mois');
        });
    }

    public function down(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->dropColumn(['heures_mois', 'jours_mois', 'diamants']);
        });
    }
};
