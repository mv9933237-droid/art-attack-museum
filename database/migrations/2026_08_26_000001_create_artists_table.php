<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('nacionalidad');
            $table->string('estado');
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_fallecimiento')->nullable();
            $table->text('biografia')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();

            $table->index('estado');
            $table->index('is_system');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
