<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'hero';

    protected $fillable = ['name', 'role'];

    protected $hidden = ['created_at', 'updated_at'];
}
