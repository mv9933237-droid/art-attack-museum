<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Location;
use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_movimiento_valido(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $origin->id,
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado para exhibición',
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'artwork_id', 'origin_location_id', 'destination_location_id'],
            ]);

        $this->assertDatabaseHas('movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
        ]);
    }

    public function test_movimiento_actualiza_ubicacion_de_obra(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $origin->id,
        ]);

        $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'current_location_id' => $destination->id,
        ]);
    }

    public function test_obra_inexistente_rechazada(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => 999999,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['artwork_id']);
    }

    public function test_ubicacion_origen_inexistente_rechazada(): void
    {
        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => 999999,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['origin_location_id']);
    }

    public function test_ubicacion_destino_inexistente_rechazada(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => 999999,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['destination_location_id']);
    }

    public function test_origen_igual_a_destino_rechazado(): void
    {
        $location = Location::create([
            'nombre' => 'Sala Principal',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $location->id,
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $location->id,
            'destination_location_id' => $location->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['destination_location_id']);
    }

    public function test_mantiene_historial_de_movimientos(): void
    {
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

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $location1->id,
        ]);

        $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $location1->id,
            'destination_location_id' => $location2->id,
            'fecha' => Carbon::today()->subDay()->toDateString(),
            'motivo' => 'Primer traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $location2->id,
            'destination_location_id' => $location3->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Segundo traslado',
            'responsable' => 'María García',
        ]);

        $this->assertDatabaseCount('movements', 2);

        $movements = Movement::where('artwork_id', $artwork->id)->get();
        $this->assertCount(2, $movements);
    }

    public function test_puede_consultar_historial_de_movimientos(): void
    {
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

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $location1->id,
        ]);

        $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $location1->id,
            'destination_location_id' => $location2->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $response = $this->getJson("/artworks/{$artwork->id}/movements");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'origin_location_id', 'destination_location_id', 'fecha'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_fecha_futura_rechazada(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $origin->id,
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::tomorrow()->toDateString(),
            'motivo' => 'Traslado',
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha']);
    }

    public function test_falta_motivo_genera_error(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $origin->id,
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::today()->toDateString(),
            'responsable' => 'Juan Pérez',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['motivo']);
    }

    public function test_falta_responsable_genera_error(): void
    {
        $origin = Location::create([
            'nombre' => 'Sala Origen',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $destination = Location::create([
            'nombre' => 'Sala Destino',
            'capacidad' => 10,
            'estado' => 'activa',
        ]);

        $artwork = Artwork::create([
            'titulo' => 'Obra Test',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
            'current_location_id' => $origin->id,
        ]);

        $response = $this->postJson('/movements', [
            'artwork_id' => $artwork->id,
            'origin_location_id' => $origin->id,
            'destination_location_id' => $destination->id,
            'fecha' => Carbon::today()->toDateString(),
            'motivo' => 'Traslado',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['responsable']);
    }
}
