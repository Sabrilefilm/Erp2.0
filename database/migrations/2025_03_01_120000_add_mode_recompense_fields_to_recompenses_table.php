<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            // TikTok : date et heure du cadeau
            $table->date('date_cadeau_tiktok')->nullable()->after('statut');
            $table->string('heure_cadeau_tiktok', 5)->nullable()->after('date_cadeau_tiktok');
            // Carte cadeau : code saisi par l'admin, récupérable par le créateur sur la facture PDF
            $table->string('code_cadeau', 255)->nullable()->after('heure_cadeau_tiktok');
            // Virement : RIB (nom, prénom, IBAN, banque) avec reconfirmation
            $table->string('rib_nom', 120)->nullable()->after('code_cadeau');
            $table->string('rib_prenom', 120)->nullable()->after('rib_nom');
            $table->string('rib_iban', 50)->nullable()->after('rib_prenom');
            $table->string('rib_banque', 120)->nullable()->after('rib_iban');
            $table->boolean('rib_confirme')->default(false)->after('rib_banque');
        });
    }

    public function down(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->dropColumn([
                'date_cadeau_tiktok',
                'heure_cadeau_tiktok',
                'code_cadeau',
                'rib_nom',
                'rib_prenom',
                'rib_iban',
                'rib_banque',
                'rib_confirme',
            ]);
        });
    }
};
