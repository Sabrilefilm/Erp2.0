<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapport_vendredis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('annee');
            $table->unsignedTinyInteger('semaine'); // 1-53 (ISO week)
            $table->text('contenu');
            $table->timestamps();

            $table->unique(['user_id', 'annee', 'semaine']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapport_vendredis');
    }
};
