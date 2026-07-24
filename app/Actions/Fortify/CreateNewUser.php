<?php

namespace App\Actions\Fortify;

use App\Models\User;
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
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'educational_emails_consent' => ['sometimes', 'accepted'],
        ])->validate();

        $educationalEmailsConsent = filter_var(
            $input['educational_emails_consent'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'tipo' => 'aluno',
            'autorizado' => false,
            'educational_emails_consent' => $educationalEmailsConsent,
            'educational_emails_consented_at' => $educationalEmailsConsent ? now() : null,
            'educational_emails_consent_revoked_at' => null,
        ]);
    }
}
