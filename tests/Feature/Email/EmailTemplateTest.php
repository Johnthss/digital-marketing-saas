<?php

namespace Tests\Feature\Email;

use App\Models\Agency;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
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
    public function test_it_lists_templates(): void
    {
        EmailTemplate::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('email.templates.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function test_it_creates_a_template(): void
    {
        $response = $this->actingAs($this->user)->post(route('email.templates.store'), [
            'name' => 'Test Template',
            'subject' => 'Test Subject',
            'category' => 'welcome',
            'html_content' => '<h1>Hello</h1>',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('email_templates', ['name' => 'Test Template']);
    }

    /** @test */
    public function test_it_validates_template_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('email.templates.store'), []);
        $response->assertSessionHasErrors(['name', 'subject', 'category', 'html_content']);
    }

    /** @test */
    public function test_it_shows_a_template(): void
    {
        $template = EmailTemplate::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('email.templates.show', $template));
        $response->assertStatus(200);
    }

    /** @test */
    public function test_it_edits_a_template(): void
    {
        $template = EmailTemplate::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('email.templates.edit', $template));
        $response->assertStatus(200);
    }

    /** @test */
    public function test_it_updates_a_template(): void
    {
        $template = EmailTemplate::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('email.templates.update', $template), [
            'name' => 'Updated Template',
            'subject' => 'Updated Subject',
            'category' => 'notification',
            'html_content' => '<h1>Updated</h1>',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('email_templates', ['id' => $template->id, 'name' => 'Updated Template']);
    }

    /** @test */
    public function test_it_deletes_a_template(): void
    {
        $template = EmailTemplate::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('email.templates.destroy', $template));
        $response->assertRedirect();
        $this->assertSoftDeleted('email_templates', ['id' => $template->id]);
    }

    /** @test */
    public function test_it_prevents_access_to_other_agency_templates(): void
    {
        $otherAgency = Agency::factory()->create();
        $template = EmailTemplate::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('email.templates.show', $template));
        $response->assertForbidden();
    }
}
