<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = ['title', 'description', 'role', 'tags', 'highlights', 'demo', 'show_demo_soon', 'image', 'order'];

    protected $hidden = ['created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'tags'           => 'array',
            'highlights'     => 'array',
            'order'          => 'integer',
            'show_demo_soon' => 'boolean',
        ];
    }
}
