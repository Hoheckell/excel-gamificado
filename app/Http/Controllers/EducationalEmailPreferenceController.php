<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EducationalEmailPreferenceController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAluno(), 403);

        $consent = $request->boolean('educational_emails_consent');
        $user = $request->user();

        $user->forceFill([
            'educational_emails_consent' => $consent,
            'educational_emails_consented_at' => $consent
                ? ($user->educational_emails_consent ? $user->educational_emails_consented_at : now())
                : $user->educational_emails_consented_at,
            'educational_emails_consent_revoked_at' => $consent
                ? null
                : ($user->educational_emails_consent ? now() : $user->educational_emails_consent_revoked_at),
        ])->save();

        return back()->with('success', $consent
            ? 'Consentimento para conteúdos educacionais registrado.'
            : 'Consentimento revogado. Você não receberá novos conteúdos educacionais opcionais.');
    }
}
