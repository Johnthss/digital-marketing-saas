<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    public function test_swagger_ui_page_loads(): void
    {
        $response = $this->get('/api/docs');
        $response->assertStatus(200);
        $response->assertSee('swagger-ui', false);
        $response->assertSee('Digital Marketing SaaS', false);
    }

    public function test_openapi_yaml_returns_spec(): void
    {
        $response = $this->get('/api/docs/openapi.yaml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/x-yaml');
        $response->assertSee('openapi: 3.0.3', false);
        $response->assertSee('Digital Marketing SaaS API', false);
    }

    public function test_openapi_json_returns_spec(): void
    {
        $response = $this->get('/api/docs/openapi.json');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
    }
}
