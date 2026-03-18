<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('createurs_adverses', function (Blueprint $table) {
            $table->id();
            $table->string('tiktok_at', 100)->unique()->comment('@ TikTok sans le @');
            $table->string('nom', 255)->nullable()->comment('Nom ou agence');
            $table->string('telephone', 50);
            $table->string('email', 255)->nullable();
            $table->text('autres_infos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('createurs_adverses');
    }
};
