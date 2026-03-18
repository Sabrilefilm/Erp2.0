<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapport_vendredis', function (Blueprint $table) {
            $table->timestamp('valide_at')->nullable()->after('contenu');
            $table->foreignId('valide_par')->nullable()->after('valide_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rapport_vendredis', function (Blueprint $table) {
            $table->dropForeign(['valide_par']);
            $table->dropColumn(['valide_at', 'valide_par']);
        });
    }
};
