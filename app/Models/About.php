<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $table = 'about';

    protected $fillable = ['bio'];

    protected $hidden = ['created_at', 'updated_at'];
}
