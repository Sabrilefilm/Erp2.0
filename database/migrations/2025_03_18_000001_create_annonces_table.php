<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('annonces', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('contenu');
            $table->string('type')->default('annonce'); // annonce, evenement, campagne
            $table->integer('ordre')->nullable();
            $table->boolean('actif')->default(true);
            
            // Champs spécifiques aux événements
            $table->datetime('date_evenement')->nullable();
            $table->string('lieu_evenement')->nullable();
            
            // Champs spécifiques aux campagnes TikTok
            $table->string('lien_tiktok')->nullable();
            $table->string('hashtag_principal')->nullable();
            $table->text('objectif_campagne')->nullable();
            $table->datetime('date_debut')->nullable();
            $table->datetime('date_fin')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('annonces');
    }
};
