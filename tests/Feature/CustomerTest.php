<?php

namespace Tests\Feature;

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
}
