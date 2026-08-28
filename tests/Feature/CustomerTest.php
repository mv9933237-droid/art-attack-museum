<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_registrar_cliente_valido(): void
    {
        $response = $this->postJson('/customers', [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'documento' => '12345678',
            'email' => 'juan@example.com',
            'telefono' => '12345678',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'apellido', 'documento'],
            ]);

        $this->assertDatabaseHas('customers', [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'documento' => '12345678',
        ]);
    }

    public function test_documento_duplicado_genera_error(): void
    {
        Customer::factory()->create(['documento' => '12345678']);

        $response = $this->postJson('/customers', [
            'nombre' => 'María',
            'apellido' => 'García',
            'documento' => '12345678',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['documento']);
    }

    public function test_puede_consultar_listado_de_clientes(): void
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nombre', 'apellido'],
                ],
            ]);
    }

    public function test_puede_consultar_detalle_de_cliente(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/customers/{$customer->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'apellido', 'documento'],
            ]);
    }

    public function test_puede_consultar_ventas_de_cliente(): void
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

        $response = $this->getJson("/customers/{$customer->id}/sales");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'customer_id', 'estado', 'total', 'moneda', 'sale_details'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_puede_consultar_ventas_de_cliente_sin_ventas(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->getJson("/customers/{$customer->id}/sales");

        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function test_cliente_inexistente_ventas_retorna_404(): void
    {
        $response = $this->getJson('/customers/999999/sales');

        $response->assertStatus(404);
    }
}
