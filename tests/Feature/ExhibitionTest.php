<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Exhibition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_exposicion_fisica_valida(): void
    {
        $response = $this->postJson('/exhibitions', [
            'nombre' => 'Exposición Test',
            'descripcion' => 'Una exposición de prueba',
            'tipo' => 'physical',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'tipo', 'start_date', 'end_date', 'estado'],
            ]);

        $this->assertDatabaseHas('exhibitions', [
            'nombre' => 'Exposición Test',
            'tipo' => 'physical',
            'estado' => 'programada',
        ]);
    }

    public function test_puede_crear_exposicion_virtual_valida(): void
    {
        $response = $this->postJson('/exhibitions', [
            'nombre' => 'Exposición Virtual Test',
            'descripcion' => 'Una exposición virtual de prueba',
            'tipo' => 'virtual',
            'url' => 'https://exhibitions.example.com/test',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'tipo', 'url'],
            ]);

        $this->assertDatabaseHas('exhibitions', [
            'nombre' => 'Exposición Virtual Test',
            'tipo' => 'virtual',
            'url' => 'https://exhibitions.example.com/test',
        ]);
    }

    public function test_tipo_exposicion_invalido_rechazado(): void
    {
        $response = $this->postJson('/exhibitions', [
            'nombre' => 'Exposición Test',
            'descripcion' => 'Una exposición de prueba',
            'tipo' => 'invalido',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo']);
    }

    public function test_fechas_invalidas_rechazadas(): void
    {
        $response = $this->postJson('/exhibitions', [
            'nombre' => 'Exposición Test',
            'descripcion' => 'Una exposición de prueba',
            'tipo' => 'physical',
            'start_date' => '2026-09-10',
            'end_date' => '2026-09-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_exposicion_virtual_requiere_url(): void
    {
        $response = $this->postJson('/exhibitions', [
            'nombre' => 'Exposición Virtual',
            'descripcion' => 'Una exposición virtual sin URL',
            'tipo' => 'virtual',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    public function test_puede_consultar_listado_de_exposiciones(): void
    {
        Exhibition::factory()->count(3)->create();

        $response = $this->getJson('/exhibitions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nombre', 'tipo'],
                ],
            ]);
    }

    public function test_puede_consultar_detalle_de_exposicion(): void
    {
        $exhibition = Exhibition::factory()->create();

        $response = $this->getJson("/exhibitions/{$exhibition->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'nombre', 'tipo', 'artworks'],
            ]);
    }

    public function test_puede_asociar_obra_a_exposicion(): void
    {
        $exhibition = Exhibition::factory()->create();
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/exhibitions/{$exhibition->id}/artworks", [
            'artwork_id' => $artwork->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'exhibition_id', 'artwork_id'],
            ]);

        $this->assertDatabaseHas('exhibition_artwork', [
            'exhibition_id' => $exhibition->id,
            'artwork_id' => $artwork->id,
        ]);
    }

    public function test_puede_consultar_obras_de_exposicion(): void
    {
        $exhibition = Exhibition::factory()->create();
        $artwork = Artwork::factory()->create();

        $exhibition->artworks()->attach($artwork);

        $response = $this->getJson("/exhibitions/{$exhibition->id}/artworks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'titulo'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_puede_consultar_exposiciones_de_obra(): void
    {
        $artwork = Artwork::factory()->create();
        $exhibition = Exhibition::factory()->create();

        $exhibition->artworks()->attach($artwork);

        $response = $this->getJson("/artworks/{$artwork->id}/exhibitions");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nombre'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_obra_inexistente_rechazada(): void
    {
        $exhibition = Exhibition::factory()->create();

        $response = $this->postJson("/exhibitions/{$exhibition->id}/artworks", [
            'artwork_id' => 999999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['artwork_id']);
    }

    public function test_exposicion_inexistente_retorna_404(): void
    {
        $response = $this->getJson('/exhibitions/999999');

        $response->assertStatus(404);
    }

    public function test_relacion_duplicada_rechazada(): void
    {
        $exhibition = Exhibition::factory()->create();
        $artwork = Artwork::factory()->create();

        $exhibition->artworks()->attach($artwork);

        $response = $this->postJson("/exhibitions/{$exhibition->id}/artworks", [
            'artwork_id' => $artwork->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_permitir_obra_en_exposiciones_virtuales_simultaneas(): void
    {
        $artwork = Artwork::factory()->create();

        $exhibition1 = Exhibition::factory()->create([
            'tipo' => 'virtual',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $exhibition2 = Exhibition::factory()->create([
            'tipo' => 'virtual',
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

    public function test_rechazar_solapamiento_exposiciones_fisicas(): void
    {
        $artwork = Artwork::factory()->create();

        $exhibition1 = Exhibition::factory()->create([
            'tipo' => 'physical',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $exhibition2 = Exhibition::factory()->create([
            'tipo' => 'physical',
            'start_date' => '2026-09-05',
            'end_date' => '2026-09-15',
        ]);

        $this->postJson("/exhibitions/{$exhibition1->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(201);

        $response = $this->postJson("/exhibitions/{$exhibition2->id}/artworks", [
            'artwork_id' => $artwork->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_permitir_exposiciones_fisicas_consecutivas(): void
    {
        $artwork = Artwork::factory()->create();

        $exhibition1 = Exhibition::factory()->create([
            'tipo' => 'physical',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-10',
        ]);

        $exhibition2 = Exhibition::factory()->create([
            'tipo' => 'physical',
            'start_date' => '2026-09-11',
            'end_date' => '2026-09-20',
        ]);

        $this->postJson("/exhibitions/{$exhibition1->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(201);

        $this->postJson("/exhibitions/{$exhibition2->id}/artworks", [
            'artwork_id' => $artwork->id,
        ])->assertStatus(201);
    }

    public function test_puede_retirar_obra_de_exposicion(): void
    {
        $exhibition = Exhibition::factory()->create();
        $artwork = Artwork::factory()->create();

        $exhibition->artworks()->attach($artwork);

        $response = $this->deleteJson("/exhibitions/{$exhibition->id}/artworks/{$artwork->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('exhibition_artwork', [
            'exhibition_id' => $exhibition->id,
            'artwork_id' => $artwork->id,
        ]);
    }

    public function test_retirar_obra_no_asociada_retorna_404(): void
    {
        $exhibition = Exhibition::factory()->create();
        $artwork = Artwork::factory()->create();

        $response = $this->deleteJson("/exhibitions/{$exhibition->id}/artworks/{$artwork->id}");

        $response->assertStatus(404);
    }
}
