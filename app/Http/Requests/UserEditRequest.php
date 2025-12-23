<?php

namespace App\Http\Requests;

use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;

class UserEditRequest extends FormRequest
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
        $userId = $this->route('id');
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'active' => ['required', 'string', 'max:1'],
        ];
    }


    public function messages(): array
    {
        return [
            'required' => 'Este campo é obrigatório',
            'string' => 'Deve ser uma string',
            'max' => 'O campo não pode ter mais caracteres',
        ];
    }
}
