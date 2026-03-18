<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->string('createur_adverse_agence', 255)->nullable()->after('createur_adverse');
            $table->string('createur_adverse_agent', 255)->nullable()->after('createur_adverse_agence');
        });

        if (Schema::hasTable('createurs_adverses')) {
            Schema::table('createurs_adverses', function (Blueprint $table) {
                if (! Schema::hasColumn('createurs_adverses', 'agence')) {
                    $table->string('agence', 255)->nullable()->after('nom');
                }
                if (! Schema::hasColumn('createurs_adverses', 'agent')) {
                    $table->string('agent', 255)->nullable()->after('agence');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('planning', function (Blueprint $table) {
            $table->dropColumn(['createur_adverse_agence', 'createur_adverse_agent']);
        });
        if (Schema::hasTable('createurs_adverses')) {
            Schema::table('createurs_adverses', function (Blueprint $table) {
                if (Schema::hasColumn('createurs_adverses', 'agence')) {
                    $table->dropColumn('agence');
                }
                if (Schema::hasColumn('createurs_adverses', 'agent')) {
                    $table->dropColumn('agent');
                }
            });
        }
    }
};
