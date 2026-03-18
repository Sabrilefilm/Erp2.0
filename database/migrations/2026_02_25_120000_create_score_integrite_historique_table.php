<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_integrite_historique', function (Blueprint $table) {
            $table->id();
            $table->timestamp('heure_modification');
            $table->text('details_infraction')->nullable();
            $table->unsignedTinyInteger('score_avant')->default(100); // score avant modification
            $table->unsignedTinyInteger('score_consequent'); // score après (0-100)
            $table->string('sanction_infraction')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_integrite_historique');
    }
};
