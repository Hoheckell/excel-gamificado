<?php

namespace App\Policies;

use App\Models\Certificado;
use App\Models\User;

class CertificadoPolicy
{
    public function create(User $user): bool
    {
        return $user->isProfessor() || $user->autorizado;
    }

    public function view(User $user, Certificado $certificado): bool
    {
        return $user->id === $certificado->user_id || $user->isProfessor();
    }
}
