<?php

namespace Tests\Feature;

use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_registrar_pago_valido(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
            'comprobante' => 'REC-001',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'sale_id', 'monto', 'metodo_pago', 'estado'],
            ]);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $sale->id,
            'monto' => 500,
            'metodo_pago' => 'efectivo',
            'estado' => 'registrado',
        ]);
    }

    public function test_pago_asociado_a_venta_existente(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $sale->id,
        ]);
    }

    public function test_pago_con_monto_invalido_rechazado(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 0,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['monto']);
    }

    public function test_pago_con_metodo_invalido_rechazado(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'invalido',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['metodo_pago']);
    }

    public function test_moneda_pago_es_bob(): void
    {
        $sale = Sale::factory()->create([
            'moneda' => 'BOB',
        ]);

        $response = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $sale->id,
            'monto' => 500,
        ]);
    }

    public function test_estado_pago_es_registrado(): void
    {
        $sale = Sale::factory()->create();

        $response = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.estado', 'registrado');
    }

    public function test_puede_consultar_pagos_de_venta(): void
    {
        $sale = Sale::factory()->create();

        $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response = $this->getJson("/sales/{$sale->id}/payments");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'monto', 'metodo_pago'],
                ],
            ]);
    }

    public function test_venta_inexistente_retorna_404(): void
    {
        $response = $this->postJson('/sales/999999/payments', [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(404);
    }
}
