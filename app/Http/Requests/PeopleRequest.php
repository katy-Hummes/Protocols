<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidCpf;
use App\Rules\AgeRequirement;

class PeopleRequest extends FormRequest
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
        $peopleId = $this->route('id');
        return [
            'name' => ['required'],
            'birth' => ['required', new AgeRequirement],
            'cpf' => ['required', 'unique:people,cpf,' . $peopleId, new ValidCpf()],
            'sex' => ['required'],
        ];
    }

    public function prepareForValidation()
{
    $this->merge([
        'cpf' => preg_replace('/[^0-9]/', '', $this->cpf),
    ]);
}

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório',
            'birth.required' => 'O campo data de nascimento é obrigatório.',
            'cpf.unique' => 'CPF já cadastrado.',
            'AgeRequirement' => 'A idade mínima é de 16 anos.',
            'ValidCpf' => 'CPF inválido.',
        ];
    }
}
