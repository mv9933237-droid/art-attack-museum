<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Customer;
use App\Models\Exhibition;
use App\Models\Location;
use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_flujo_completo_artista_a_venta(): void
    {
        $artistResponse = $this->postJson('/artists', [
            'nombre' => 'Vincent',
            'apellido' => 'VanGogh',
            'nacionalidad' => 'Neerlandesa',
            'estado' => 'activo',
        ]);
        $artistResponse->assertStatus(201);
        $artist = $artistResponse->json('data');

        $artworkResponse = $this->postJson('/artworks', [
            'titulo' => 'La Noche Estrellada',
            'naturaleza' => 'original',
            'anio_creacion' => 1889,
        ]);
        $artworkResponse->assertStatus(201);
        $artwork = $artworkResponse->json('data');

        $this->postJson("/artworks/{$artwork['id']}/artists", [
            'artist_id' => $artist['id'],
            'tipo_autoria' => 'confirmada',
        ])->assertStatus(201);

        $this->getJson("/artworks/{$artwork['id']}")
            ->assertJsonPath('data.artists.0.id', $artist['id']);

        $location1Response = $this->postJson('/locations', [
            'nombre' => 'Sala Principal',
            'capacidad' => 50,
        ]);
        $location1 = $location1Response->json('data');

        $location2Response = $this->postJson('/locations', [
            'nombre' => 'Sala Temporal',
            'capacidad' => 30,
        ]);
        $location2 = $location2Response->json('data');

        $this->postJson('/movements', [
            'artwork_id' => $artwork['id'],
            'origin_location_id' => $location1['id'],
            'destination_location_id' => $location2['id'],
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado inicial',
            'responsable' => 'Director',
        ])->assertStatus(201);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork['id'],
            'current_location_id' => $location2['id'],
        ]);

        $exhibitionResponse = $this->postJson('/exhibitions', [
            'nombre' => 'Exposicion VanGogh',
            'descripcion' => 'Retrospectiva',
            'tipo' => 'physical',
            'start_date' => Carbon::today()->addMonth()->toDateString(),
            'end_date' => Carbon::today()->addMonths(2)->toDateString(),
        ]);
        $exhibition = $exhibitionResponse->json('data');

        $this->postJson("/exhibitions/{$exhibition['id']}/artworks", [
            'artwork_id' => $artwork['id'],
        ])->assertStatus(201);

        $customerResponse = $this->postJson('/customers', [
            'nombre' => 'Maria',
            'apellido' => 'Garcia',
            'documento' => '12345678',
        ]);
        $customer = $customerResponse->json('data');

        $reservationResponse = $this->postJson('/reservations', [
            'artwork_id' => $artwork['id'],
            'customer_id' => $customer['id'],
        ]);
        $reservation = $reservationResponse->json('data');

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork['id'],
            'estado_comercial' => 'reservada',
        ]);

        $this->postJson("/reservations/{$reservation['id']}/cancel")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork['id'],
            'estado_comercial' => 'disponible',
        ]);

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer['id'],
            'details' => [
                [
                    'artwork_id' => $artwork['id'],
                    'precio' => 50000,
                    'impuesto' => 6500,
                ],
            ],
        ]);
        $saleResponse->assertStatus(201);
        $sale = $saleResponse->json('data');

        $this->putJson("/sales/{$sale['id']}/confirm")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork['id'],
            'estado_comercial' => 'vendida',
        ]);

        $this->postJson("/sales/{$sale['id']}/payments", [
            'monto' => 56500,
            'metodo_pago' => 'transferencia',
            'comprobante' => 'TRANS-001',
        ])->assertStatus(201);

        $this->getJson("/sales/{$sale['id']}")
            ->assertJsonPath('data.estado', 'confirmada')
            ->assertJsonPath('data.moneda', 'BOB');
    }

    public function test_reserva_bloquea_venta_directa(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'reservada',
        ]);

        $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ])->assertStatus(422);
    }

    public function test_cancelar_reserva_devuelve_estado(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $reservationResponse = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ]);
        $reservation = $reservationResponse->json('data');

        $this->postJson("/reservations/{$reservation['id']}/cancel")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_anular_venta_devuelve_estado(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

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

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'vendida',
        ]);

        $this->putJson("/sales/{$sale['id']}/annul")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_obra_original_no_doble_venta(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
            'naturaleza' => 'original',
        ]);

        $customer = Customer::factory()->create();

        $sale1Response = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);
        $sale1 = $sale1Response->json('data');

        $this->putJson("/sales/{$sale1['id']}/confirm")->assertStatus(200);

        $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 2000,
                ],
            ],
        ])->assertStatus(422);
    }

    public function test_historial_movimientos_persiste(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $location1 = Location::create([
            'nombre' => 'Sala 1',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $location2 = Location::create([
            'nombre' => 'Sala 2',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $location3 = Location::create([
            'nombre' => 'Sala 3',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $location1->id,
            'destination_location_id' => $location2->id,
            'fecha' => Carbon::today()->subDays(2)->toDateString(),
            'motivo' => 'Primer traslado',
            'responsable' => 'Director',
        ])->assertStatus(201);

        $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $location2->id,
            'destination_location_id' => $location3->id,
            'fecha' => Carbon::today()->subDay()->toDateString(),
            'motivo' => 'Segundo traslado',
            'responsable' => 'Director',
        ])->assertStatus(201);

        $this->assertDatabaseCount('movements', 2);

        $movements = Movement::where('artwork_id', $artwork->id)->get();
        $this->assertCount(2, $movements);
    }

    public function test_solapamiento_exposiciones_fisicas(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $exhibition1 = Exhibition::factory()->physical()->create([
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $exhibition2 = Exhibition::factory()->physical()->create([
            'start_date' => '2026-09-05',
            'end_date' => '2026-09-15',
        ]);

        $this->postJson("/exhibitions/{$exhibition1->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(201);

        $this->postJson("/exhibitions/{$exhibition2->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(422);
    }

    public function test_exposiciones_virtuales_simultaneas(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $exhibition1 = Exhibition::factory()->virtual()->create([
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $exhibition2 = Exhibition::factory()->virtual()->create([
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $this->postJson("/exhibitions/{$exhibition1->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(201);

        $this->postJson("/exhibitions/{$exhibition2->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(201);
    }

    public function test_transiciones_estado_obra(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $reservationResponse = $this->postJson('/reservations', [
            'artwork_id' => $artwork->id,
            'customer_id' => $customer->id,
        ]);
        $reservation = $reservationResponse->json('data');

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'reservada',
        ]);

        $this->postJson("/reservations/{$reservation['id']}/cancel")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
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

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'vendida',
        ]);

        $this->putJson("/sales/{$sale['id']}/annul")->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_autor_desconocido_funciona(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $autorDesconocido = Artist::autorDesconocido();

        $this->postJson("/artworks/{$artwork['id']}/unknown-author")->assertStatus(201);

        $this->assertDatabaseHas('artwork_artists', [
            'artwork_id' => $artwork['id'],
            'artist_id' => $autorDesconocido->id,
            'tipo_autoria' => 'confirmada',
        ]);
    }

    public function test_cliente_documento_unico(): void
    {
        Customer::factory()->create(['documento' => '12345678']);

        $this->postJson('/customers', [
            'nombre' => 'Otro',
            'apellido' => 'Cliente',
            'documento' => '12345678',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['documento']);
    }

    public function test_moneda_venta_es_bob(): void
    {
        $artwork = Artwork::factory()->create([
            'estado_comercial' => 'disponible',
        ]);

        $customer = Customer::factory()->create();

        $saleResponse = $this->postJson('/sales', [
            'customer_id' => $customer->id,
            'details' => [
                [
                    'artwork_id' => $artwork->id,
                    'precio' => 1000,
                ],
            ],
        ]);

        $this->assertEquals('BOB', $saleResponse->json('data.moneda'));
    }
}
