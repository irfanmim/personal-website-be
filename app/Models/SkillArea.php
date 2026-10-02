<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillArea extends Model
{
    /** Pillars are fixed (colours and badges live in the frontend). */
    public const PILLARS = ['engineering', 'product', 'delivery', 'leadership'];

    protected $fillable = ['key', 'label', 'short_label', 'pillar', 'level', 'tech', 'visible', 'order'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'tech'    => 'array',
            'visible' => 'boolean',
            'level'   => 'integer',
            'order'   => 'integer',
        ];
    }

    /**
     * Serialize to the API-expected shape, mapping short_label -> shortLabel.
     */
    public function toApiArray(): array
    {
        return [
            'key'        => $this->key,
            'label'      => $this->label,
            'shortLabel' => $this->short_label,
            'pillar'     => $this->pillar,
            'level'      => $this->level,
            'tech'       => $this->tech ?? [],
            'visible'    => $this->visible,
        ];
    }

    /** Starting areas for a fresh install (mirrors the frontend defaults). */
    public static function defaults(): array
    {
        return [
            ['key' => 'frontend',     'label' => 'Frontend',                    'short_label' => 'Frontend',     'pillar' => 'engineering', 'level' => 9, 'tech' => ['Vue.js', 'React', 'Cytoscape.js', 'JavaScript'], 'visible' => true],
            ['key' => 'backend',      'label' => 'Backend & APIs',              'short_label' => 'Backend',      'pillar' => 'engineering', 'level' => 7, 'tech' => ['Spring Boot', 'Django', 'Laravel', 'Java', 'Python', 'PHP'], 'visible' => true],
            ['key' => 'architecture', 'label' => 'Architecture',                'short_label' => 'Architecture', 'pillar' => 'engineering', 'level' => 9, 'tech' => ['Microservices', 'Event-Driven Architecture', 'CQRS', 'Event Sourcing', 'Apache Kafka'], 'visible' => true],
            ['key' => 'data',         'label' => 'Data & Graph',                'short_label' => 'Data',         'pillar' => 'engineering', 'level' => 5, 'tech' => ['SQL', 'Neo4j (proof-of-concept)', 'Graph visualization'], 'visible' => true],
            ['key' => 'devops',       'label' => 'DevOps & Cloud',              'short_label' => 'DevOps',       'pillar' => 'engineering', 'level' => 5, 'tech' => ['Docker', 'Kubernetes', 'Azure', 'Jenkins (CI/CD)'], 'visible' => true],
            ['key' => 'ownership',    'label' => 'Product Ownership',           'short_label' => 'Product',      'pillar' => 'product',     'level' => 9, 'tech' => ['Product ownership', 'Scrum (PSPO I)', 'Localization (i18n)', 'QA coordination'], 'visible' => true],
            ['key' => 'stakeholders', 'label' => 'Stakeholder Management',      'short_label' => 'Stakeholders', 'pillar' => 'product',     'level' => 6, 'tech' => ['Stakeholder alignment'], 'visible' => false],
            ['key' => 'projects',     'label' => 'Project Management',          'short_label' => 'Projects',     'pillar' => 'delivery',    'level' => 7, 'tech' => ['Release management', '16 production releases', 'Lean 5-person teams'], 'visible' => true],
            ['key' => 'mentoring',    'label' => 'Team Leadership & Mentoring', 'short_label' => 'Mentoring',    'pillar' => 'leadership',  'level' => 7, 'tech' => ['Led 4 engineers', 'Code reviews', 'Onboarded 6 engineers'], 'visible' => true],
            ['key' => 'presenting',   'label' => 'Presentation & Communication', 'short_label' => 'Presenting',  'pillar' => 'leadership',  'level' => 5, 'tech' => ['Demos', 'Stakeholder communication'], 'visible' => false],
        ];
    }
}
