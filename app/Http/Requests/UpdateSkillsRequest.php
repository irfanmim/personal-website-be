<?php

namespace App\Http\Requests;

use App\Models\SkillArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skills'               => ['required', 'array', 'max:40'],
            'skills.*.key'         => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_-]+$/', 'distinct'],
            'skills.*.label'       => ['required', 'string', 'max:80'],
            'skills.*.shortLabel'  => ['required', 'string', 'max:30'],
            'skills.*.pillar'      => ['required', Rule::in(SkillArea::PILLARS)],
            'skills.*.level'       => ['required', 'integer', 'between:1,10'],
            'skills.*.tech'        => ['present', 'array', 'max:20'],
            'skills.*.tech.*'      => ['string', 'max:60'],
            'skills.*.visible'     => ['required', 'boolean'],
        ];
    }
}
