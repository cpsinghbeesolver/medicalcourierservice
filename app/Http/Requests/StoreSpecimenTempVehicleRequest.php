<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSpecimenTempVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
            
            'status' => [
                'required',
                'boolean',
            ],
            'company_id' => [
                'required',
                'exists:users,id',
            ]
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please select a name.',
            'status.required' => 'Please select a status.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_id' => auth()->id(),
        ]);
    }
}
