<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:500'],
            'tags'        => ['required', 'array'],
            'tags.*'      => ['string'],
            'demo'        => ['nullable', 'url', 'max:300'],
            'image'       => ['nullable', 'string', 'max:500'],
        ];
    }
}
