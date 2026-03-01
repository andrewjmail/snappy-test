<?php

namespace App\Http\Requests;

use App\Rules\UkPostcode;
use Illuminate\Foundation\Http\FormRequest;

class StoreSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postcode' => ['required', new UkPostcode, 'string', 'min:5', 'max:8'],
            'radius' => ['sometimes', 'numeric', 'min:1', 'max:100'],
        ];
    }
}
