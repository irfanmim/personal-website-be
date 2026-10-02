<?php

namespace Tests\Feature;

use App\Models\SkillArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SkillsTest extends TestCase
{
    use RefreshDatabase;

    private function area(array $overrides = []): array
    {
        return array_merge([
            'key'        => 'frontend',
            'label'      => 'Frontend',
            'shortLabel' => 'Frontend',
            'pillar'     => 'engineering',
            'level'      => 8,
            'tech'       => ['Vue.js', 'React'],
            'visible'    => true,
        ], $overrides);
    }

    public function test_updating_skills_requires_auth(): void
    {
        $this->putJson('/api/content/skills', ['skills' => [$this->area()]])->assertUnauthorized();
    }

    public function test_skills_round_trip_through_content_and_drop_removed_areas(): void
    {
        SkillArea::create(SkillArea::defaults()[0] + ['order' => 0]);
        SkillArea::create(SkillArea::defaults()[1] + ['order' => 1]);
        Sanctum::actingAs(User::factory()->create(['username' => 'tester']));

        $payload = [
            'skills' => [
                $this->area(['key' => 'backend', 'label' => 'Backend & APIs', 'shortLabel' => 'Backend', 'level' => 3, 'visible' => false]),
                $this->area(['key' => 'new-area', 'label' => 'New', 'shortLabel' => 'New', 'pillar' => 'leadership', 'level' => 10, 'tech' => []]),
            ],
        ];

        $this->putJson('/api/content/skills', $payload)->assertOk()->assertJsonCount(2);

        $skills = $this->getJson('/api/content')->assertOk()->json('skills');
        $this->assertSame(['backend', 'new-area'], array_column($skills, 'key'));
        $this->assertSame(3, $skills[0]['level']);
        $this->assertFalse($skills[0]['visible']);
        $this->assertSame('New', $skills[1]['shortLabel']);
        $this->assertDatabaseMissing('skill_areas', ['key' => 'frontend']);
    }

    public function test_invalid_level_and_pillar_are_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create(['username' => 'tester']));

        $this->putJson('/api/content/skills', ['skills' => [$this->area(['level' => 11])]])
            ->assertUnprocessable()->assertJsonValidationErrors('skills.0.level');
        $this->putJson('/api/content/skills', ['skills' => [$this->area(['pillar' => 'other'])]])
            ->assertUnprocessable()->assertJsonValidationErrors('skills.0.pillar');
        $this->putJson('/api/content/skills', ['skills' => [$this->area(), $this->area()]])
            ->assertUnprocessable();
    }
}
