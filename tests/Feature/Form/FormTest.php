<?php

namespace Tests\Feature\Form;

use App\Models\Agency;
use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormTest extends TestCase
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
    public function it_lists_forms(): void
    {
        Form::factory()->count(3)->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('forms.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_form(): void
    {
        $response = $this->actingAs($this->user)->post(route('forms.store'), [
            'name' => 'Test Form',
            'slug' => 'test-form',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('forms', ['name' => 'Test Form']);
    }

    /** @test */
    public function it_validates_form_creation(): void
    {
        $response = $this->actingAs($this->user)->post(route('forms.store'), []);
        $response->assertSessionHasErrors(['name', 'slug']);
    }

    /** @test */
    public function it_shows_a_form(): void
    {
        $form = Form::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('forms.show', $form));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_a_form(): void
    {
        $form = Form::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('forms.destroy', $form));
        $response->assertRedirect();
        $this->assertSoftDeleted('forms', ['id' => $form->id]);
    }

    /** @test */
    public function it_prevents_access_to_other_agency_forms(): void
    {
        $otherAgency = Agency::factory()->create();
        $form = Form::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('forms.show', $form));
        $response->assertForbidden();
    }

    /** @test */
    public function it_requires_auth(): void
    {
        $response = $this->get(route('forms.index'));
        $response->assertRedirect(route('login'));
    }
}
