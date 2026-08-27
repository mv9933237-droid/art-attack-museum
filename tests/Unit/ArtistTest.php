<?php

namespace Tests\Unit;

use App\Models\Artist;
use PHPUnit\Framework\TestCase;

class ArtistTest extends TestCase
{
    public function test_is_system_returns_true_for_system_artist(): void
    {
        $artist = new Artist;
        $artist->is_system = true;

        $this->assertTrue($artist->isSystem());
    }

    public function test_is_system_returns_false_for_regular_artist(): void
    {
        $artist = new Artist;
        $artist->is_system = false;

        $this->assertFalse($artist->isSystem());
    }

    public function test_is_active_returns_true_when_active(): void
    {
        $artist = new Artist;
        $artist->estado = Artist::ESTADO_ACTIVO;

        $this->assertTrue($artist->isActive());
    }

    public function test_is_active_returns_false_when_inactive(): void
    {
        $artist = new Artist;
        $artist->estado = Artist::ESTADO_INACTIVO;

        $this->assertFalse($artist->isActive());
    }

    public function test_estado_constants_are_correct(): void
    {
        $this->assertEquals('activo', Artist::ESTADO_ACTIVO);
        $this->assertEquals('inactivo', Artist::ESTADO_INACTIVO);
    }
}
