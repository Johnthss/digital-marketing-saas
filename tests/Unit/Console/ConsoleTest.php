<?php

namespace Tests\Unit\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_runs_migrate_command(): void
    {
        $this->artisan('migrate')->assertSuccessful();
    }

    /** @test */
    public function it_runs_route_list_command(): void
    {
        $this->artisan('route:list')->assertSuccessful();
    }

    /** @test */
    public function it_runs_config_clear_command(): void
    {
        $this->artisan('config:clear')->assertSuccessful();
    }
}
