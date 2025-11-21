<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSecretRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Encrypted content from client
            'content' => 'required|string|max:100000',

            // Flag to require password when reading
            'requires_password' => 'sometimes|boolean',

            // Expiration time in hours
            'expires_in_hours' => 'sometimes|nullable|integer|min:1|max:168',

            // Optional array of encrypted files
            'files' => 'sometimes|array|max:5',
            'files.*.encrypted_name' => 'required|string|max:500',
            'files.*.file_data' => 'required|string',
        ];
    }
}