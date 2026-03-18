<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_question_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_question_id')->constrained('formation_questions')->cascadeOnDelete();
            $table->text('texte');
            $table->boolean('est_correcte')->default(false);
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_question_reponses');
    }
};
