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
            // P1.1: El blob cifrado que nos manda el cliente
            'content' => 'required|string|max:100000', // Texto, max 100KB

            // P1.4: El flag que nos dice si debemos pedir contraseña al leer
            'requires_password' => 'sometimes|boolean',

            // P2.2: Opciones de expiración
            'expires_in_hours' => 'sometimes|nullable|integer|min:1|max:168',
        ];
    }
}
