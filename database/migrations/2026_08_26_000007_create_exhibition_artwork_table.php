<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhibition_artwork', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exhibition_id');
            $table->unsignedBigInteger('artwork_id');
            $table->timestamps();

            $table->foreign('exhibition_id')->references('id')->on('exhibitions')->cascadeOnDelete();
            $table->foreign('artwork_id')->references('id')->on('artworks')->cascadeOnDelete();
            $table->unique(['exhibition_id', 'artwork_id']);
            $table->index('exhibition_id');
            $table->index('artwork_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhibition_artwork');
    }
};
