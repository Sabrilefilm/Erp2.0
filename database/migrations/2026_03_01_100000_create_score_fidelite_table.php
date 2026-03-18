<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_fidelite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained('createurs')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->default(0);
            $table->timestamp('palier_80_debloque_at')->nullable();
            $table->timestamp('palier_100_debloque_at')->nullable();
            $table->timestamps();
            $table->unique('createur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_fidelite');
    }
};
