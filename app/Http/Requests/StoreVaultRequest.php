<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVaultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vaults', 'name')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                }),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'icon' => [
                'nullable',
                'string',
                'max:10',
            ],
            'is_default' => [
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du coffre est obligatoire.',
            'name.unique' => 'Vous avez déjà un coffre avec ce nom.',
            'name.max' => 'Le nom du coffre ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'icon.max' => 'L\'icône ne peut pas dépasser 10 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_default' => $this->boolean('is_default', false),
        ]);
    }
}