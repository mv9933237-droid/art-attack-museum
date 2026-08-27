<?php

namespace Tests\Feature;

use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorDesconocidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_autor_desconocido_exists_and_is_unique(): void
    {
        $autor1 = Artist::autorDesconocido();
        $autor2 = Artist::autorDesconocido();

        $this->assertTrue($autor1->isSystem());
        $this->assertEquals($autor1->id, $autor2->id);
        $this->assertEquals(1, Artist::where('is_system', true)->count());
    }

    public function test_autor_desconocido_has_correct_data(): void
    {
        $autor = Artist::autorDesconocido();

        $this->assertEquals('AUTOR', $autor->nombre);
        $this->assertEquals('DESCONOCIDO', $autor->apellido);
        $this->assertEquals('No especificada', $autor->nacionalidad);
        $this->assertEquals(Artist::ESTADO_ACTIVO, $autor->estado);
        $this->assertTrue($autor->isSystem());
    }

    public function test_autor_desconocido_is_active(): void
    {
        $autor = Artist::autorDesconocido();

        $this->assertTrue($autor->isActive());
    }

    public function test_autor_desconocido_not_in_catalog(): void
    {
        Artist::autorDesconocido();

        $response = $this->getJson('/artists');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_cannot_create_duplicate_system_artist(): void
    {
        $autor1 = Artist::autorDesconocido();

        $autor2 = Artist::autorDesconocido();

        $this->assertEquals($autor1->id, $autor2->id);
        $this->assertEquals(1, Artist::where('is_system', true)->count());
    }
}
