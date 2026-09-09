<?php

namespace Tests\Feature\Docs;

use Tests\TestCase;

class DocsTest extends TestCase
{
    public function test_it_shows_api_docs(): void
    {
        $response = $this->get('/api/docs');
        $response->assertStatus(200);
    }

    public function test_it_returns_openapi_yaml(): void
    {
        $response = $this->get('/api/docs/openapi.yaml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-yaml');
    }

    public function test_it_returns_openapi_json(): void
    {
        $response = $this->get('/api/docs/openapi.json');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }
}