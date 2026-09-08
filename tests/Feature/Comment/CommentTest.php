<?php

namespace Tests\Feature\Comment;

use App\Models\Agency;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private Agency $agency;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = Agency::factory()->create();
        $this->user = User::factory()->create(['agency_id' => $this->agency->id]);
    }

    /** @test */
    public function it_lists_comments(): void
    {
        Comment::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('comments.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_comment(): void
    {
        $response = $this->actingAs($this->user)->post(route('comments.store'), [
            'content' => 'Test comment',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', ['content' => 'Test comment']);
    }

    /** @test */
    public function it_validates_comment_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('comments.store'), []);
        $response->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function it_deletes_a_comment(): void
    {
        $comment = Comment::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('comments.destroy', $comment));
        $response->assertRedirect();
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_comments(): void
    {
        $otherAgency = Agency::factory()->create();
        $comment = Comment::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('comments.show', $comment));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('comments.index'));
        $response->assertRedirect(route('login'));
    }
}
