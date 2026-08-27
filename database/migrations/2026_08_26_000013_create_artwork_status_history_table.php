<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artwork_id');
            $table->string('estado_anterior');
            $table->string('estado_nuevo');
            $table->string('responsable')->nullable();
            $table->timestamps();

            $table->foreign('artwork_id')->references('id')->on('artworks');
            $table->index('artwork_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_status_history');
    }
};
