<?php

namespace Tests\Feature;

use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtistCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_valid_artist_and_consult_in_catalog(): void
    {
        $payload = [
            'nombre' => 'Pablo',
            'apellido' => 'Picasso',
            'nacionalidad' => 'Española',
            'estado' => Artist::ESTADO_ACTIVO,
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'nombre' => 'Pablo',
                'apellido' => 'Picasso',
                'nacionalidad' => 'Española',
                'estado' => Artist::ESTADO_ACTIVO,
            ]);

        $this->assertDatabaseHas('artists', [
            'nombre' => 'Pablo',
            'apellido' => 'Picasso',
        ]);

        $response = $this->getJson('/artists');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'nombre' => 'Pablo',
                'apellido' => 'Picasso',
            ]);
    }

    public function test_reject_missing_nombre(): void
    {
        $payload = [
            'apellido' => 'Picasso',
            'nacionalidad' => 'Española',
            'estado' => Artist::ESTADO_ACTIVO,
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_reject_missing_apellido(): void
    {
        $payload = [
            'nombre' => 'Pablo',
            'nacionalidad' => 'Española',
            'estado' => Artist::ESTADO_ACTIVO,
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['apellido']);
    }

    public function test_reject_missing_nacionalidad(): void
    {
        $payload = [
            'nombre' => 'Pablo',
            'apellido' => 'Picasso',
            'estado' => Artist::ESTADO_ACTIVO,
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nacionalidad']);
    }

    public function test_reject_missing_estado(): void
    {
        $payload = [
            'nombre' => 'Pablo',
            'apellido' => 'Picasso',
            'nacionalidad' => 'Española',
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);
    }

    public function test_reject_invalid_estado(): void
    {
        $payload = [
            'nombre' => 'Pablo',
            'apellido' => 'Picasso',
            'nacionalidad' => 'Española',
            'estado' => 'invalido',
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estado']);
    }

    public function test_accept_optional_fields(): void
    {
        $payload = [
            'nombre' => 'Frida',
            'apellido' => 'Kahlo',
            'nacionalidad' => 'Mexicana',
            'estado' => Artist::ESTADO_ACTIVO,
            'fecha_nacimiento' => '1907-07-06',
            'fecha_fallecimiento' => '1954-07-13',
            'biografia' => 'Pintora mexicana conocida por sus autorretratos.',
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'nombre' => 'Frida',
                'apellido' => 'Kahlo',
                'nacionalidad' => 'Mexicana',
                'estado' => Artist::ESTADO_ACTIVO,
                'biografia' => 'Pintora mexicana conocida por sus autorretratos.',
            ]);

        $this->assertDatabaseHas('artists', [
            'nombre' => 'Frida',
            'apellido' => 'Kahlo',
        ]);

        $artist = Artist::where('nombre', 'Frida')->first();
        $this->assertEquals('1907-07-06', $artist->fecha_nacimiento->format('Y-m-d'));
        $this->assertEquals('1954-07-13', $artist->fecha_fallecimiento->format('Y-m-d'));
    }

    public function test_accept_optional_fields_null(): void
    {
        $payload = [
            'nombre' => 'Claude',
            'apellido' => 'Monet',
            'nacionalidad' => 'Francesa',
            'estado' => Artist::ESTADO_ACTIVO,
        ];

        $response = $this->postJson('/artists', $payload);

        $response->assertStatus(201);

        $artist = $this->assertDatabaseHas('artists', [
            'nombre' => 'Claude',
            'apellido' => 'Monet',
        ]);

        $this->assertNull(Artist::where('nombre', 'Claude')->first()->fecha_nacimiento);
        $this->assertNull(Artist::where('nombre', 'Claude')->first()->fecha_fallecimiento);
        $this->assertNull(Artist::where('nombre', 'Claude')->first()->biografia);
    }

    public function test_active_inactive_state(): void
    {
        $artist = Artist::factory()->activo()->create();

        $this->assertTrue($artist->isActive());

        $artist->update(['estado' => Artist::ESTADO_INACTIVO]);
        $artist->refresh();

        $this->assertFalse($artist->isActive());
    }

    public function test_catalog_excludes_system_artists(): void
    {
        Artist::autorDesconocido();
        Artist::factory()->count(3)->create();

        $response = $this->getJson('/artists');

        $response->assertOk()
            ->assertJsonCount(3, 'data');

        $data = $response->json('data');
        foreach ($data as $artist) {
            $this->assertFalse($artist['is_system']);
        }
    }

    public function test_show_single_artist(): void
    {
        $artist = Artist::factory()->create([
            'nombre' => 'Salvador',
            'apellido' => 'Dali',
        ]);

        $response = $this->getJson("/artists/{$artist->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'nombre' => 'Salvador',
                'apellido' => 'Dali',
            ]);
    }
}
