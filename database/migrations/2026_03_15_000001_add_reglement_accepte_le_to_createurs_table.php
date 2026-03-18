<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->timestamp('reglement_accepte_le')->nullable()->after('contrat_signe_le');
        });
    }

    public function down(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->dropColumn('reglement_accepte_le');
        });
    }
};
