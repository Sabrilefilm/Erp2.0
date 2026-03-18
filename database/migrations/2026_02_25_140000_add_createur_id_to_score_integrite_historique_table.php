<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('score_integrite_historique', function (Blueprint $table) {
            $table->foreignId('createur_id')->nullable()->after('id')->constrained('createurs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('score_integrite_historique', function (Blueprint $table) {
            $table->dropForeign(['createur_id']);
        });
    }
};
