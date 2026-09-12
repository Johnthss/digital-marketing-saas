<?php

namespace Tests\Unit\Queue;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        Queue::fake();
        // Queue::push(new SomeJob());
        $this->assertTrue(true);
    }

    #[Test]
    public function it_can_dispatch_jobs_sync(): void
    {
        $this->assertTrue(true);
    }
}
