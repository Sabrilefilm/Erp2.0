<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('createurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('email')->nullable();
            $table->string('pseudo_tiktok')->nullable();
            $table->string('statut', 64)->nullable();
            $table->foreignId('equipe_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ambassadeur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('missions')->nullable();
            $table->unsignedBigInteger('stats_vues')->default(0);
            $table->unsignedBigInteger('stats_followers')->default(0);
            $table->decimal('stats_engagement', 10, 2)->nullable();
            $table->timestamp('date_import')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('createurs');
    }
};
