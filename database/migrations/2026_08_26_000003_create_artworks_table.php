<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('naturaleza');
            $table->string('estado_comercial')->default('disponible');
            $table->string('dimensiones')->nullable();
            $table->string('tecnica')->nullable();
            $table->integer('anio_creacion')->nullable();
            $table->unsignedBigInteger('current_location_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->foreign('current_location_id')->references('id')->on('locations')->nullOnDelete();
            $table->index('estado_comercial');
            $table->index('naturaleza');
            $table->index('current_location_id');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
