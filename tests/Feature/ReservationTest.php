<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_reserva_valida(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $response = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'artwork_id', 'customer_id', 'estado'],
            ]);

        $this->assertDatabaseHas('reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
            'estado' => 'activa',
        ]);
    }

    public function test_reserva_cambia_estado_obra_a_reservada(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'reservada',
        ]);
    }

    public function test_obra_no_disponible_rechazada(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'reservada',
        ]);

        $customer = Customer::factory()->create();

        $response = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_obra_inexistente_rechazada(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson('/reservations', [
            'artwork_id' => 999999,
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['artwork_id']);
    }

    public function test_cliente_inexistente_rechazado(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => 999999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_id']);
    }

    public function test_puede_cancelar_reserva(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'reservada',
        ]);

        $customer = Customer::factory()->create();

        $reservation = Reservation::create([
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
            'estado' => Reservation::ESTADO_ACTIVA,
        ]);

        $response = $this->postJson("/reservations/{$reservation->id}/cancel");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'estado'],
            ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'estado' => 'cancelada',
        ]);
    }

    public function test_cancelar_reserva_devuelve_obra_a_disponible(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'reservada',
        ]);

        $customer = Customer::factory()->create();

        $reservation = Reservation::create([
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
            'estado' => Reservation::ESTADO_ACTIVA,
        ]);

        $this->postJson("/reservations/{$reservation->id}/cancel");

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_cancelar_reserva_no_activa_rechazada(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'reservada',
        ]);

        $customer = Customer::factory()->create();

        $reservation = Reservation::create([
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
            'estado' => Reservation::ESTADO_CANCELADA,
        ]);

        $response = $this->postJson("/reservations/{$reservation->id}/cancel");

        $response->assertStatus(422);
    }

    public function test_puede_consultar_listado_de_reservas(): void
    {
        Reservation::factory()->count(3)->create();

        $response = $this->getJson('/reservations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'artwork_id', 'customer_id', 'estado'],
                ],
            ]);
    }

    public function test_puede_consultar_detalle_de_reserva(): void
    {
        $reservation = Reservation::factory()->create();

        $response = $this->getJson("/reservations/{$reservation->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'artwork_id', 'customer_id', 'estado'],
            ]);
    }

    public function test_reserva_con_cliente_y_obra_validos(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $response = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
            'estado' => 'activa',
        ]);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'reservada',
        ]);
    }

    public function test_obra_ya_reservada_rechazada(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer1->id,
        ])->assertStatus(201);

        $response = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer2->id,
        ]);

        $response->assertStatus(422);
    }
}
