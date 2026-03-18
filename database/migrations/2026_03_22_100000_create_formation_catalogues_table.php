<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_catalogues', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('label', 255);
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        // Catalogues initiaux (équivalents aux anciennes constantes)
        DB::table('formation_catalogues')->insert([
            ['slug' => 'tiktok', 'label' => 'Découvrir TikTok', 'ordre' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'projet_personnel', 'label' => 'Projet personnel', 'ordre' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'autres', 'label' => 'Autres', 'ordre' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_catalogues');
    }
};
