<?php

namespace App\Policies;

use App\Models\Turma;
use App\Models\User;

class TurmaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Turma $turma): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isProfessor();
    }

    public function update(User $user, Turma $turma): bool
    {
        return $this->professorDaTurma($user, $turma);
    }

    public function delete(User $user, Turma $turma): bool
    {
        return $this->professorDaTurma($user, $turma);
    }

    public function concluir(User $user, Turma $turma): bool
    {
        return $this->professorDaTurma($user, $turma);
    }

    private function professorDaTurma(User $user, Turma $turma): bool
    {
        if (! $user->isProfessor()) {
            return false;
        }

        return $user->turmas()->where('turmas.id', $turma->id)->exists();
    }
}
