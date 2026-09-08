<?php

namespace Tests\Feature\Version;

use Tests\TestCase;

class VersionTest extends TestCase
{
    /** @test */
    public function it_returns_version_info(): void
    {
        $response = $this->getJson('/api/version');
        $response->assertOk();
        $response->assertJsonStructure(['version', 'name']);
    }

    /** @test */
    public function it_returns_changelog(): void
    {
        $response = $this->getJson('/api/changelog');
        $response->assertOk();
    }

    /** @test */
    public function it_returns_health_status(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertOk();
        $response->assertJson(['status' => 'ok']);
    }
}
