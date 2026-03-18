<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->foreignId('updated_par')->nullable()->after('cree_par')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->dropForeign(['updated_par']);
        });
    }
};
