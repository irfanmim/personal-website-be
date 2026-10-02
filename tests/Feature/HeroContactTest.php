<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HeroContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_hero_and_contact_requires_auth(): void
    {
        $this->putJson('/api/content/hero', [])->assertUnauthorized();
        $this->putJson('/api/content/contact', [])->assertUnauthorized();
    }

    public function test_hero_copy_fields_round_trip_through_content(): void
    {
        Sanctum::actingAs(User::factory()->create(['username' => 'tester']));

        $this->putJson('/api/content/hero', [
            'name'     => 'Jane Doe',
            'role'     => 'Engineer',
            'tagline'  => 'Builds things.',
            'greeting' => 'Hello!',
            'headline' => "I'm Jane",
        ])->assertOk()->assertJsonPath('greeting', 'Hello!');

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('hero.headline', "I'm Jane")
            ->assertJsonPath('hero.role', 'Engineer');
    }

    public function test_contact_copy_fields_round_trip_through_content(): void
    {
        Sanctum::actingAs(User::factory()->create(['username' => 'tester']));

        $this->putJson('/api/content/contact', [
            'heading'  => 'Say hi',
            'blurb'    => 'Reach out any time.',
            'linkedin' => 'https://linkedin.com/in/jane',
            'github'   => 'https://github.com/jane',
        ])->assertOk()->assertJsonPath('heading', 'Say hi');

        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('contact.blurb', 'Reach out any time.');
    }

    public function test_empty_contact_has_copy_keys(): void
    {
        $this->getJson('/api/content')
            ->assertOk()
            ->assertJsonPath('contact.heading', '')
            ->assertJsonPath('contact.blurb', '');
    }
}
