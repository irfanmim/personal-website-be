<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExperienceRole extends Model
{
    protected $table = 'experience_roles';

    protected $fillable = ['role', 'order'];

    protected $hidden = ['created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(ExperienceCompany::class, 'experience_id')->orderBy('order');
    }
}
