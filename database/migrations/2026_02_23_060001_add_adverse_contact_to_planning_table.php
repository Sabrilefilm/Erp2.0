<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            if (!Schema::hasColumn('planning', 'createur_adverse_email')) {
                $table->string('createur_adverse_email', 255)->nullable()->after('createur_adverse_numero');
            }
            if (!Schema::hasColumn('planning', 'createur_adverse_autres')) {
                $table->text('createur_adverse_autres')->nullable()->after('createur_adverse_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->dropColumn(['createur_adverse_email', 'createur_adverse_autres']);
        });
    }
};
