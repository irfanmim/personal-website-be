<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceCompany extends Model
{
    protected $table = 'experience_companies';

    protected $fillable = ['experience_id', 'summary', 'company', 'period', 'order'];

    protected $hidden = ['experience_id', 'created_at', 'updated_at'];

    protected $appends = ['experienceId'];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function getExperienceIdAttribute(): int
    {
        return $this->attributes['experience_id'];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(ExperienceRole::class, 'experience_id');
    }
}
