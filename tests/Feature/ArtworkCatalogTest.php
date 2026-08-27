<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\ArtworkStatusHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtworkCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_registrar_obra_con_campos_validos(): void
    {
        $response = $this->postJson('/artworks', [
            'titulo' => 'La Mona Lisa',
            'naturaleza' => 'original',
            'descripcion' => 'Obra maestra del Renacimiento',
            'dimensiones' => '77cm x 53cm',
            'tecnica' => 'Óleo sobre madera',
            'anio_creacion' => 1503,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'titulo',
                    'naturaleza',
                    'estado_comercial',
                ],
            ]);

        $this->assertDatabaseHas('artworks', [
            'titulo' => 'La Mona Lisa',
            'naturaleza' => 'original',
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_obra_se_crea_con_estado_disponible(): void
    {
        $response = $this->postJson('/artworks', [
            'titulo' => 'Obra Test',
            'naturaleza' => 'replica',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('artworks', [
            'titulo' => 'Obra Test',
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_falta_titulo_genera_error(): void
    {
        $response = $this->postJson('/artworks', [
            'naturaleza' => 'original',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['titulo']);
    }

    public function test_falta_naturaleza_genera_error(): void
    {
        $response = $this->postJson('/artworks', [
            'titulo' => 'Obra Sin Naturaleza',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['naturaleza']);
    }

    public function test_naturaleza_invalida_genera_error(): void
    {
        $response = $this->postJson('/artworks', [
            'titulo' => 'Obra Test',
            'naturaleza' => 'invalida',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['naturaleza']);
    }

    public function test_puede_consultar_listado_de_obras(): void
    {
        Artwork::factory()->count(3)->create();

        $response = $this->getJson('/artworks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'titulo'],
                ],
            ]);
    }

    public function test_puede_filtrar_obras_por_estado(): void
    {
        Artwork::factory()->create(['estado_comercial' => 'disponible']);
        Artwork::factory()->create(['estado_comercial' => 'vendida']);

        $response = $this->getJson('/artworks?status=disponible');

        $response->assertStatus(200);

        $data = $response->json('data');
        foreach ($data as $obra) {
            $this->assertEquals('disponible', $obra['estado_comercial']);
        }
    }

    public function test_puede_consultar_detalle_de_obra(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->getJson("/artworks/{$artwork->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'titulo',
                    'naturaleza',
                    'estado_comercial',
                ],
            ]);
    }

    public function test_obra_inexistente_retorna_404(): void
    {
        $response = $this->getJson('/artworks/999999');

        $response->assertStatus(404);
    }

    public function test_puede_editar_obra(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->putJson("/artworks/{$artwork->id}", [
            'titulo' => 'Titulo Actualizado',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'titulo' => 'Titulo Actualizado',
        ]);
    }

    public function test_puede_eliminar_obra_logicamente(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->deleteJson("/artworks/{$artwork->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('artworks', ['id' => $artwork->id]);
    }

    public function test_cambiar_estado_a_reservada_es_valido(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'disponible']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'reservada',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'reservada',
        ]);
    }

    public function test_cambiar_estado_a_vendida_desde_disponible_es_valido(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'disponible']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'vendida',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'vendida',
        ]);
    }

    public function test_cambiar_estado_a_no_disponible_es_valido(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'disponible']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'no_disponible',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'no_disponible',
        ]);
    }

    public function test_transicion_invalida_genera_error(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'vendida']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'reservada',
        ]);

        $response->assertStatus(422);
    }

    public function test_transicion_reservada_a_dispensible_es_valida(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'reservada']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'disponible',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_transicion_reservada_a_vendida_es_valida(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'reservada']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'vendida',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'vendida',
        ]);
    }

    public function test_transicion_vendida_a_dispensible_es_valida(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'vendida']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'disponible',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_transicion_no_disponible_a_dispensible_es_valida(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'no_disponible']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'disponible',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'estado_comercial' => 'disponible',
        ]);
    }

    public function test_historial_de_estados_se_registra(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'disponible']);

        $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'reservada',
        ]);

        $this->assertDatabaseHas('artwork_status_history', [
            'artwork_id' => $artwork->id,
            'estado_anterior' => 'disponible',
            'estado_nuevo' => 'reservada',
        ]);
    }

    public function test_historial_registra_multiples_transiciones(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'disponible']);

        $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'reservada',
        ]);

        $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'vendida',
        ]);

        $this->assertDatabaseCount('artwork_status_history', 2);

        $historial = ArtworkStatusHistory::where('artwork_id', $artwork->id)->get();

        $this->assertEquals('disponible', $historial[0]->estado_anterior);
        $this->assertEquals('reservada', $historial[0]->estado_nuevo);

        $this->assertEquals('reservada', $historial[1]->estado_anterior);
        $this->assertEquals('vendida', $historial[1]->estado_nuevo);
    }

    public function test_estado_invalido_genera_error(): void
    {
        $artwork = Artwork::factory()->create(['estado_comercial' => 'disponible']);

        $response = $this->putJson("/artworks/{$artwork->id}/status", [
            'estado_comercial' => 'estado_invalido',
        ]);

        $response->assertStatus(422);
    }

    public function test_puede_filtrar_obras_por_titulo(): void
    {
        Artwork::factory()->create(['titulo' => 'La Mona Lisa']);
        Artwork::factory()->create(['titulo' => 'La Última Cena']);

        $response = $this->getJson('/artworks?search=Mona');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('La Mona Lisa', $data[0]['titulo']);
    }
}
