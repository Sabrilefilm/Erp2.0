<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->string('media_path', 500)->nullable()->after('url');
            $table->string('fichier_path', 500)->nullable()->after('media_path');
            $table->string('fichier_nom', 255)->nullable()->after('fichier_path');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn(['media_path', 'fichier_path', 'fichier_nom']);
        });
    }
};
