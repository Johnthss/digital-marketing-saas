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
    public function it_creates_form(): void
    {
        $response = $this->actingAs($this->user)->post(route('forms.store'), [
            'name' => 'Contact Form',
            'fields' => [['name' => 'email', 'type' => 'email']],
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('forms', ['name' => 'Contact Form']);
    }

    /** @test */
    public function it_shows_form(): void
    {
        $form = Form::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->get(route('forms.show', $form));
        $response->assertStatus(200);
    }

    /** @test */
    public function it_updates_form(): void
    {
        $form = Form::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->put(route('forms.update', $form), [
            'name' => 'Updated Form',
        ]);
        $response->assertRedirect();
    }

    /** @test */
    public function it_deletes_form(): void
    {
        $form = Form::factory()->create(['agency_id' => $this->agency->id]);
        $response = $this->actingAs($this->user)->delete(route('forms.destroy', $form));
        $response->assertRedirect();
        $this->assertSoftDeleted('forms', ['id' => $form->id]);
    }

    /** @test */
    public function it_toggles_form_publication(): void
    {
        $form = Form::factory()->create(['agency_id' => $this->agency->id, 'is_published' => false]);
        $response = $this->actingAs($this->user)->post(route('forms.toggle', $form));
        $response->assertRedirect();
        $this->assertDatabaseHas('forms', ['id' => $form->id, 'is_published' => true]);
    }

    /** @test */
    public function it_prevents_unauthorized_access(): void
    {
        $otherAgency = Agency::factory()->create();
        $form = Form::factory()->create(['agency_id' => $otherAgency->id]);
        $response = $this->actingAs($this->user)->get(route('forms.show', $form));
        $response->assertForbidden();
    }
}
