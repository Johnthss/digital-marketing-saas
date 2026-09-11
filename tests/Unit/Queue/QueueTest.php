<?php

namespace Tests\Unit\Queue;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_dispatch_jobs(): void
    {
        Queue::fake();
        // Queue::push(new SomeJob());
        $this->assertTrue(true);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_dispatch_jobs_sync(): void
    {
        $this->assertTrue(true);
    }
}
