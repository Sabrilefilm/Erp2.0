<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            if (! Schema::hasColumn('formations', 'mots_cles')) {
                $table->text('mots_cles')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            if (Schema::hasColumn('formations', 'mots_cles')) {
                $table->dropColumn('mots_cles');
            }
        });
    }
};
