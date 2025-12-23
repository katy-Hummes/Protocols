<?php

namespace App\Http\Requests;

use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'type' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'max:14', 'unique:users', new ValidCpf()],
        ];
    }

    public function prepareForValidation()
{
    $this->merge([
        'cpf' => preg_replace('/[^0-9]/', '', $this->cpf),
    ]);
}

    public function messages(): array
    {
        return [
            'required' => 'Este campo é obrigatório',
            'email' => 'Deve ser um email válido',
            'unique' => 'Este :attribute já está em uso',
            'confirmed' => 'As senhas não coincidem',
            'min' => 'A senha deve ter no mínimo 8 caracteres',
            'max' => 'O campo não pode ter mais de 255 caracteres',
            'ValidCpf' => 'O CPF informado é inválido',
        ];
    }
}
