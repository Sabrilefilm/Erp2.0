<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->string('niveau_match', 32)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->dropColumn('niveau_match');
        });
    }
};
