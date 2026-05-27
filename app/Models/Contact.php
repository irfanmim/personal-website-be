<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';

    protected $fillable = ['linkedin', 'github', 'instagram', 'cv_url'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    /**
     * Serialize to the API-expected shape, mapping cv_url -> cvUrl.
     */
    public function toApiArray(): array
    {
        return [
            'linkedin'  => $this->linkedin,
            'github'    => $this->github,
            'instagram' => $this->instagram,
            'cvUrl'     => $this->cv_url,
        ];
    }
}
