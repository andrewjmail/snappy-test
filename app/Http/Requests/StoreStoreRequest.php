<?php

namespace App\Http\Requests;

use App\Enums\StoreBrand;
use App\Traits\HandlesApiResponses;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreStoreRequest extends FormRequest
{
    use HandlesApiResponses;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stores')->where(fn ($query) => $query->where('address', $this->address))->ignore($this->route('store')),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', new Enum(StoreBrand::class)],
            'delivery_radius_km' => ['required', 'numeric', 'min:0.1', 'max:100'],

            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A store with the same name and address already exists.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error('Validation failed', 422, $validator->errors())
        );
    }
}
