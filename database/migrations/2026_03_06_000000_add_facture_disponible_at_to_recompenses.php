<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->timestamp('facture_disponible_at')->nullable()->after('quantite_carte_cadeau');
        });
    }

    public function down(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->dropColumn('facture_disponible_at');
        });
    }
};
