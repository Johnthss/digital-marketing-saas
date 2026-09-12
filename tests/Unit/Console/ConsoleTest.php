<?php

namespace Tests\Unit\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConsoleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_runs_migrate_command(): void
    {
        $this->artisan('migrate')->assertSuccessful();
    }

    #[Test]
    public function it_runs_route_list_command(): void
    {
        $this->artisan('route:list')->assertSuccessful();
    }

    #[Test]
    public function it_runs_config_clear_command(): void
    {
        $this->artisan('config:clear')->assertSuccessful();
    }
}
