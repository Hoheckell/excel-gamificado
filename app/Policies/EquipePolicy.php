<?php

namespace App\Policies;

use App\Models\Equipe;
use App\Models\User;

class EquipePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Equipe $equipe): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        if (!$user->isProfessor()) {
            return false;
        }

        return $user->turmas()->exists();
    }

    public function update(User $user, Equipe $equipe): bool
    {
        return $this->professorDaTurma($user, $equipe);
    }

    public function delete(User $user, Equipe $equipe): bool
    {
        return $this->professorDaTurma($user, $equipe);
    }

    public function managePoints(User $user, Equipe $equipe): bool
    {
        return $this->professorDaTurma($user, $equipe);
    }

    public function manageAlunos(User $user, Equipe $equipe): bool
    {
        return $this->professorDaTurma($user, $equipe);
    }

    private function professorDaTurma(User $user, Equipe $equipe): bool
    {
        if (!$user->isProfessor()) {
            return false;
        }

        return $user->turmas()
            ->where('turmas.id', $equipe->turma_id)
            ->exists();
    }
}
