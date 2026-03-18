<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_match', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained()->cascadeOnDelete();
            $table->date('date_souhaitee');
            $table->string('type', 64);
            $table->string('message', 500)->nullable();
            $table->string('statut', 32)->default('en_attente'); // en_attente, programmee, refusee
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_match');
    }
};
