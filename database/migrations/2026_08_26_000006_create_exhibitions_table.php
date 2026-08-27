<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exhibitions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('tipo');
            $table->string('url', 500)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('estado')->default('programada');
            $table->timestamps();

            $table->index('tipo');
            $table->index(['start_date', 'end_date']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exhibitions');
    }
};
