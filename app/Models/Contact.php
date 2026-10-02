<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';

    protected $fillable = ['heading', 'blurb', 'linkedin', 'github', 'instagram', 'cv_url'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    /**
     * Serialize to the API-expected shape, mapping cv_url -> cvUrl.
     */
    public function toApiArray(): array
    {
        return [
            'heading'   => $this->heading,
            'blurb'     => $this->blurb,
            'linkedin'  => $this->linkedin,
            'github'    => $this->github,
            'instagram' => $this->instagram,
            'cvUrl'     => $this->cv_url,
        ];
    }
}
