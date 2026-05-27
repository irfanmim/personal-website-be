<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'summary' => ['required', 'string', 'max:150'],
            'company' => ['required', 'string', 'max:150'],
            'period'  => ['required', 'string', 'max:50'],
        ];
    }
}
