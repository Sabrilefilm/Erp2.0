<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->timestamp('contrat_signe_le')->nullable()->after('date_import');
        });
    }

    public function down(): void
    {
        Schema::table('createurs', function (Blueprint $table) {
            $table->dropColumn('contrat_signe_le');
        });
    }
};
