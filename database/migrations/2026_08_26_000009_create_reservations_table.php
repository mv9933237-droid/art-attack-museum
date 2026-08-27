<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('artwork_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('estado')->default('activa');
            $table->timestamps();

            $table->foreign('artwork_id')->references('id')->on('artworks');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->index('artwork_id');
            $table->index('customer_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
