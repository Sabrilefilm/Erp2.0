<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->decimal('montant_carte_cadeau', 8, 2)->nullable()->after('type_carte_cadeau');
            $table->unsignedSmallInteger('quantite_carte_cadeau')->default(1)->after('montant_carte_cadeau');
        });
    }

    public function down(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->dropColumn(['montant_carte_cadeau', 'quantite_carte_cadeau']);
        });
    }
};
