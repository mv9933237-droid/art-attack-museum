<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_artists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artwork_id');
            $table->unsignedBigInteger('artist_id');
            $table->string('tipo_autoria');
            $table->timestamps();

            $table->foreign('artwork_id')->references('id')->on('artworks')->cascadeOnDelete();
            $table->foreign('artist_id')->references('id')->on('artists')->cascadeOnDelete();
            $table->unique(['artwork_id', 'artist_id']);
            $table->index('artwork_id');
            $table->index('artist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_artists');
    }
};
