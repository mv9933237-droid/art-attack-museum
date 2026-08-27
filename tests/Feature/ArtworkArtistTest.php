<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtworkArtistTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_asociar_artista_a_obra(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'artwork_id', 'artist_id', 'tipo_autoria'],
            ]);

        $this->assertDatabaseHas('artwork_artists', [
            'artwork_id' => $artwork->id,
            'artist_id' => $artist->id,
            'tipo_autoria' => 'confirmada',
        ]);
    }

    public function test_puede_asociar_multiples_artistas_a_obra(): void
    {
        $artwork = Artwork::factory()->create();
        $artist1 = Artist::factory()->create();
        $artist2 = Artist::factory()->create();

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist1->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist2->id,
            'tipo_autoria' => 'atribuida',
        ]);

        $this->assertDatabaseCount('artwork_artists', 2);

        $artists = $artwork->artists;
        $this->assertCount(2, $artists);
    }

    public function test_artista_inexistente_rechazado(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => 999999,
            'tipo_autoria' => 'confirmada',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['artist_id']);
    }

    public function test_autor_desconocido_correctamente_asociado(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/unknown-author");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'artwork_id', 'artist_id', 'tipo_autoria'],
            ]);

        $autorDesconocido = Artist::autorDesconocido();

        $this->assertDatabaseHas('artwork_artists', [
            'artwork_id' => $artwork->id,
            'artist_id' => $autorDesconocido->id,
            'tipo_autoria' => 'confirmada',
        ]);
    }

    public function test_no_duplicar_relaciones(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'atribuida',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['artist_id']);

        $this->assertDatabaseCount('artwork_artists', 1);
    }

    public function test_puede_modificar_artistas_de_obra(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $response = $this->deleteJson("/artworks/{$artwork->id}/artists/{$artist->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('artwork_artists', [
            'artwork_id' => $artwork->id,
            'artist_id' => $artist->id,
        ]);
    }

    public function test_puede_consultar_artistas_de_obra(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $response = $this->getJson("/artworks/{$artwork->id}/artists");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'nombre', 'apellido', 'pivot'],
                ],
            ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($artist->id, $data[0]['id']);
    }

    public function test_tipo_autoria_confirmada_valido(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('artwork_artists', [
            'tipo_autoria' => 'confirmada',
        ]);
    }

    public function test_tipo_autoria_atribuida_valido(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'atribuida',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('artwork_artists', [
            'tipo_autoria' => 'atribuida',
        ]);
    }

    public function test_tipo_autoria_invalido_rechazado(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
            'tipo_autoria' => 'invalida',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_autoria']);
    }

    public function test_falta_artist_id_genera_error(): void
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'tipo_autoria' => 'confirmada',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['artist_id']);
    }

    public function test_falta_tipo_autoria_genera_error(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tipo_autoria']);
    }

    public function test_eliminar_relacion_inexistente_retorna_404(): void
    {
        $artwork = Artwork::factory()->create();
        $artist = Artist::factory()->create();

        $response = $this->deleteJson("/artworks/{$artwork->id}/artists/{$artist->id}");

        $response->assertStatus(404);
    }

    public function test_autor_desconocido_ya_asociado_retorna_mensaje(): void
    {
        $artwork = Artwork::factory()->create();

        $this->postJson("/artworks/{$artwork->id}/unknown-author");

        $response = $this->postJson("/artworks/{$artwork->id}/unknown-author");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
            ]);

        $this->assertDatabaseCount('artwork_artists', 1);
    }

    public function test_obra_con_artistas_se_consulta_correctamente(): void
    {
        $artwork = Artwork::factory()->create();
        $artist1 = Artist::factory()->create();
        $artist2 = Artist::factory()->create();

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist1->id,
            'tipo_autoria' => 'confirmada',
        ]);

        $this->postJson("/artworks/{$artwork->id}/artists", [
            'artist_id' => $artist2->id,
            'tipo_autoria' => 'atribuida',
        ]);

        $response = $this->getJson("/artworks/{$artwork->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'titulo', 'artists'],
            ]);

        $artists = $response->json('data.artists');
        $this->assertCount(2, $artists);
    }
}
