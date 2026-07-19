<?php

namespace App\Policies;

use App\Models\Missao;
use App\Models\User;

class MissaoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Missao $missao): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isProfessor();
    }

    public function update(User $user, Missao $missao): bool
    {
        return $user->isProfessor();
    }

    public function delete(User $user, Missao $missao): bool
    {
        return $user->isProfessor();
    }

    public function atribuirEquipes(User $user, Missao $missao): bool
    {
        return $user->isProfessor();
    }
}
