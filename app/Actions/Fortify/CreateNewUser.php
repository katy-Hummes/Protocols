<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\ValidCpf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $userActive = auth()->user()->active;
        if ($userActive === 'N') {
            return redirect()->back();
        }
        // dd($input);
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'type' => ['required', 'string', 'max:1'],
            'cpf' => ['required', 'max:14', new ValidCpf()],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'type' => $input['type'] ?? 'default_value',
            'cpf' => $input['cpf'],
            'active' === 'S'
        ]);
    }
}
