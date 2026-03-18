<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->string('type_carte_cadeau', 64)->nullable()->after('code_cadeau');
        });
    }

    public function down(): void
    {
        Schema::table('recompenses', function (Blueprint $table) {
            $table->dropColumn('type_carte_cadeau');
        });
    }
};
