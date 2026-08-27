<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_venta_valida(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                    'impuesto' => 130,
                    'descuento' => 0,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'customer_id', 'estado', 'total', 'moneda'],
            ]);

        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'estado' => 'pendiente',
            'moneda' => 'BOB',
        ]);
    }

    public function test_venta_cambia_estado_obra_a_vendida(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $saleId = $response->json('data.id');

        $this->putJson("/sales/{$saleId}/confirm")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'vendida',
        ]);
    }

    public function test_cliente_inexistente_rechazado(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => 999999,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_id']);
    }

    public function test_venta_sin_detalles_rechazada(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['details']);
    }

    public function test_obra_inexistente_rechazada(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => 999999,
                    'precio' => 1000,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['details.0.artwork_id']);
    }

    public function test_obra_no_disponible_rechazada(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'reservada',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_calculo_correcto_totales(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                    'impuesto' => 130,
                    'descuento' => 50,
                ],
            ],
        ]);

        $sale = Sale::find($response->json('data.id'));

        $this->assertEquals(1080, $sale->total);
    }

    public function test_moneda_es_bob(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $this->assertEquals('BOB', $response->json('data.moneda'));
    }

    public function test_puede_confirmar_venta(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $saleId = $response->json('data.id');

        $response = $this->putJson("/sales/{$saleId}/confirm");

        $response->assertStatus(200)
            ->assertJsonPath('data.estado', 'confirmada');
    }

    public function test_confirmar_venta_no_pendiente_rechazada(): void
    {
        $sale = Sale::factory()->create([
            'estado' => Sale::ESTADO_CONFIRMADA,
        ]);

        $response = $this->putJson("/sales/{$sale->id}/confirm");

        $response->assertStatus(422);
    }

    public function test_puede_anular_venta(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $saleId = $response->json('data.id');

        $this->putJson("/sales/{$saleId}/confirm")->assertStatus(200);

        $response = $this->putJson("/sales/{$saleId}/annul");

        $response->assertStatus(200)
            ->assertJsonPath('data.estado', 'anulada');
    }

    public function test_anular_venta_devuelve_obra_a_disponible(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $saleId = $response->json('data.id');

        $this->putJson("/sales/{$saleId}/confirm")->assertStatus(200);
        $this->putJson("/sales/{$saleId}/annul")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_anular_venta_no_confirmada_rechazada(): void
    {
        $sale = Sale::factory()->create([
            'estado' => Sale::ESTADO_PENDIENTE,
        ]);

        $response = $this->putJson("/sales/{$sale->id}/annul");

        $response->assertStatus(422);
    }

    public function test_exclusividad_obra_original(): void
    {
        $customer = Customer::factory()->create();
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'original',
        ]);

        $sale1 = Sale::factory()->create([
            'customer_id' => $customer->id,
            'estado' => Sale::ESTADO_CONFIRMADA,
        ]);

        SaleDetail::factory()->create([
            'sale_id' => $sale1->id,
            'artwork_id' => $artwork->id,
            'precio' => 1000,
            'subtotal' => 1000,
        ]);

        $response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 2000,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }
}
