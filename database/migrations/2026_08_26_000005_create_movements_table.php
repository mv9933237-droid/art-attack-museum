<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artwork_id');
            $table->unsignedBigInteger('origin_location_id')->nullable();
            $table->unsignedBigInteger('destination_location_id')->nullable();
            $table->date('fecha');
            $table->text('motivo');
            $table->string('responsable');
            $table->timestamps();

            $table->foreign('artwork_id')->references('id')->on('artworks');
            $table->foreign('origin_location_id')->references('id')->on('locations')->nullOnDelete();
            $table->foreign('destination_location_id')->references('id')->on('locations')->nullOnDelete();
            $table->index('artwork_id');
            $table->index('origin_location_id');
            $table->index('destination_location_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
