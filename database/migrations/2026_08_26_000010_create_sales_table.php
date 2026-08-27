<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('estado')->default('pendiente');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuesto_total', 12, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('moneda')->default('BOB');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->index('customer_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
