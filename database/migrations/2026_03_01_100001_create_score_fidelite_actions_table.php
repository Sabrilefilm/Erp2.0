<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_fidelite_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('createur_id')->constrained('createurs')->cascadeOnDelete();
            $table->string('action_type', 80);
            $table->unsignedSmallInteger('points');
            $table->string('source_type', 80)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['createur_id', 'source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_fidelite_actions');
    }
};
