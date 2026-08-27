<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_registrar_ubicacion_valida(): void
    {
        $response = $this->postJson('/locations', [
            'nombre' => 'Sala Principal',
            'descripcion' => 'Sala de exhibición principal',
            'capacidad' => 50,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'capacidad', 'estado'],
            ]);

        $this->assertDatabaseHas('locations', [
            'nombre' => 'Sala Principal',
            'capacidad' => 50,
            'estado' => 'activa',
        ]);
    }

    public function test_falta_nombre_genera_error(): void
    {
        $response = $this->postJson('/locations', [
            'capacidad' => 50,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_falta_capacidad_genera_error(): void
    {
        $response = $this->postJson('/locations', [
            'nombre' => 'Sala Test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['capacidad']);
    }

    public function test_nombre_duplicado_genera_error(): void
    {
        Location::create([
            'nombre' => 'Sala Principal',
            'capacidad' => 50,
            'estado' => 'activa',
        ]);

        $response = $this->postJson('/locations', [
            'nombre' => 'Sala Principal',
            'capacidad' => 30,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_puede_consultar_listado_de_ubicaciones(): void
    {
        Location::factory()->count(3)->create();

        $response = $this->getJson('/locations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nombre', 'capacidad'],
                ],
            ]);
    }

    public function test_puede_consultar_detalle_de_ubicacion(): void
    {
        $location = Location::create([
            'nombre' => 'Sala Principal',
            'capacidad' => 50,
            'estado' => 'activa',
        ]);

        $response = $this->getJson("/locations/{$location->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'capacidad', 'artworks'],
            ]);
    }

    public function test_ubicacion_inexistente_retorna_404(): void
    {
        $response = $this->getJson('/locations/999999');

        $response->assertStatus(404);
    }

    public function test_puede_editar_ubicacion(): void
    {
        $location = Location::create([
            'nombre' => 'Sala Principal',
            'capacidad' => 50,
            'estado' => 'activa',
        ]);

        $response = $this->putJson("/locations/{$location->id}", [
            'nombre' => 'Sala Principal Actualizada',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'nombre' => 'Sala Principal Actualizada',
        ]);
    }

    public function test_puede_consultar_obras_de_ubicacion(): void
    {
        $location = Location::create([
            'nombre' => 'Sala Principal',
            'capacidad' => 50,
            'estado' => 'activa',
        ]);

        Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $location->id,
        ]);

        $response = $this->getJson("/locations/{$location->id}/artworks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'titulo'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_ubicacion_se_crea_con_estado_activa(): void
    {
        $response = $this->postJson('/locations', [
            'nombre' => 'Sala Nueva',
            'capacidad' => 30,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('locations', [
            'nombre' => 'Sala Nueva',
            'estado' => 'activa',
        ]);
    }

    public function test_capacidad_cero_es_valida(): void
    {
        $response = $this->postJson('/locations', [
            'nombre' => 'Sala Vacía',
            'capacidad' => 0,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('locations', [
            'nombre' => 'Sala Vacía',
            'capacidad' => 0,
        ]);
    }
}
