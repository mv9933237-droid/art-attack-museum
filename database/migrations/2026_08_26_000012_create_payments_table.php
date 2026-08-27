<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago');
            $table->text('comprobante')->nullable();
            $table->string('estado')->default('registrado');
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales');
            $table->index('sale_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
