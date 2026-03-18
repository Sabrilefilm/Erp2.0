<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demandes_match', function (Blueprint $table) {
            $table->string('heure_souhaitee', 5)->nullable()->after('date_souhaitee');
            $table->string('qui_en_face', 255)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('demandes_match', function (Blueprint $table) {
            $table->dropColumn(['heure_souhaitee', 'qui_en_face']);
        });
    }
};
