<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreSocialPostRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormRequestTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_social_post_request_has_rules(): void
    {
        $request = new StoreSocialPostRequest;
        $this->assertArrayHasKey('content', $request->rules());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_social_post_request_has_messages(): void
    {
        $request = new StoreSocialPostRequest;
        $this->assertIsArray($request->messages());
    }
}
