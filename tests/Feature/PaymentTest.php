<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function createConfirmedSaleWithTotal(float $total): array
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'replica',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => $total,
                ],
            ],
        ]);
        $sale = $saleResponse->json('data');
        $this->putJson("/sales/{$sale['id']}/confirm")->assertStatus(200);

        return $sale;
    }

    public function test_puede_registrar_pago_valido(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $response = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
            'comprobante' => 'REC-001',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'sale_id', 'monto', 'metodo_pago', 'estado'],
            ]);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $sale['id'],
            'monto' => 500,
            'metodo_pago' => 'efectivo',
            'estado' => 'registrado',
        ]);
    }

    public function test_pago_asociado_a_venta_existente(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $response = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $sale['id'],
        ]);
    }

    public function test_pago_con_monto_invalido_rechazado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $response = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 0,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['monto']);
    }

    public function test_pago_con_metodo_invalido_rechazado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $response = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'invalido',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['metodo_pago']);
    }

    public function test_moneda_pago_es_bob(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $response = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'sale_id' => $sale['id'],
            'monto' => 500,
        ]);
    }

    public function test_estado_pago_es_registrado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $response = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.estado', 'registrado');
    }

    public function test_puede_consultar_pagos_de_venta(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ]);

        $response = $this->getJson("/sales/{$sale['id']}/payments");

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

    public function test_verificar_pago_registrado_a_verificado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);
        $payment = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $response = $this->putJson("/payments/{$payment['id']}/verify");

        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', [
            'id' => $payment['id'],
            'estado' => 'verificado',
        ]);
    }

    public function test_rechazar_pago_registrado_a_rechazado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);
        $payment = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $response = $this->putJson("/payments/{$payment['id']}/reject");

        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', [
            'id' => $payment['id'],
            'estado' => 'rechazado',
        ]);
    }

    public function test_no_verificar_pago_ya_verificado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);
        $payment = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $this->putJson("/payments/{$payment['id']}/verify")->assertStatus(200);

        $response = $this->putJson("/payments/{$payment['id']}/verify");
        $response->assertStatus(422);
    }

    public function test_no_rechazar_pago_ya_verificado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);
        $payment = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $this->putJson("/payments/{$payment['id']}/verify")->assertStatus(200);

        $response = $this->putJson("/payments/{$payment['id']}/reject");
        $response->assertStatus(422);
    }

    public function test_no_verificar_pago_rechazado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);
        $payment = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $this->putJson("/payments/{$payment['id']}/reject")->assertStatus(200);

        $response = $this->putJson("/payments/{$payment['id']}/verify");
        $response->assertStatus(422);
    }

    public function test_no_rechazar_pago_rechazado(): void
    {
        $sale = $this->createConfirmedSaleWithTotal(1000);
        $payment = $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $this->putJson("/payments/{$payment['id']}/reject")->assertStatus(200);

        $response = $this->putJson("/payments/{$payment['id']}/reject");
        $response->assertStatus(422);
    }

    public function test_pago_excede_total_venta_rechazado(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'replica',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);
        $sale = $saleResponse->json('data');
        $this->putJson("/sales/{$sale['id']}/confirm")->assertStatus(200);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 1500,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'El monto excede el saldo pendiente de la venta. Saldo disponible: 1000.');
    }

    public function test_pagos_parciales_sumados_no_exceden_total(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'replica',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);
        $sale = $saleResponse->json('data');
        $this->putJson("/sales/{$sale['id']}/confirm")->assertStatus(200);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 600,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(201);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 400,
            'metodo_pago' => 'transferencia',
        ])->assertStatus(201);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 1,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(422);
    }

    public function test_pago_venta_anulada_rechazado(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'replica',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);
        $sale = $saleResponse->json('data');
        $this->putJson("/sales/{$sale['id']}/confirm")->assertStatus(200);
        $this->putJson("/sales/{$sale['id']}/annul")->assertStatus(200);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'No se pueden registrar pagos para una venta en estado anulada.');
    }

    public function test_pago_venta_pendiente_permitido(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'replica',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);
        $sale = $saleResponse->json('data');

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(201);
    }

    public function test_pago_monto_cero_rechazado(): void
    {
        $sale = Sale::factory()->create([
            'estado' => Sale::ESTADO_PENDIENTE,
            'total' => 1000,
        ]);

        $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 0,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['monto']);
    }

    public function test_pago_monto_negativo_rechazado(): void
    {
        $sale = Sale::factory()->create([
            'estado' => Sale::ESTADO_PENDIENTE,
            'total' => 1000,
        ]);

        $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => -100,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['monto']);
    }

    public function test_pago_saldo_exacto_permitido(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'replica',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);
        $sale = $saleResponse->json('data');
        $this->putJson("/sales/{$sale['id']}/confirm")->assertStatus(200);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 1000,
            'metodo_pago' => 'efectivo',
        ])->assertStatus(201);
    }

    public function test_transiciones_pago_siguen_funcionando(): void
    {
        $sale = Sale::factory()->create([
            'estado' => Sale::ESTADO_PENDIENTE,
            'total' => 1000,
        ]);

        $payment = $this->postJson("/sales/{$sale->id}/payments", [
            'monto' => 500,
            'metodo_pago' => 'efectivo',
        ])->json('data');

        $this->putJson("/payments/{$payment['id']}/verify")->assertStatus(200);
        $this->assertDatabaseHas('payments', ['id' => $payment['id'], 'estado' => 'verificado']);

        $this->putJson("/payments/{$payment['id']}/reject")->assertStatus(422);
    }
}
