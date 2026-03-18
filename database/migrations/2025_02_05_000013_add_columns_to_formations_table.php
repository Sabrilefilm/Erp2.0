<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            if (! Schema::hasColumn('formations', 'type')) {
                $table->string('type', 32)->default('video');
            }
            if (! Schema::hasColumn('formations', 'url')) {
                $table->string('url', 500)->nullable();
            }
            if (! Schema::hasColumn('formations', 'ordre')) {
                $table->unsignedSmallInteger('ordre')->default(0);
            }
            if (! Schema::hasColumn('formations', 'actif')) {
                $table->boolean('actif')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $columns = ['type', 'url', 'ordre', 'actif'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('formations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
