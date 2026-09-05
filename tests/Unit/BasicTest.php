<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function test_phpunit_works(): void
    {
        $this->assertTrue(true);
    }

    public function test_math_works(): void
    {
        $this->assertEquals(4, 2 + 2);
    }
}
