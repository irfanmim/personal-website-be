<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'linkedin'  => ['required', 'url', 'max:300'],
            'github'    => ['required', 'url', 'max:300'],
            'instagram' => ['nullable', 'url', 'max:300'],
            'cvUrl'     => ['nullable', 'url', 'max:300'],
        ];
    }
}
