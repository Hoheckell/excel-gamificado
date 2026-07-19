<?php

namespace App\Policies;

use App\Models\User;

class AlunoPolicy
{
    /**
     * Alunos podem ver a listagem (apenas nomes e equipes da sua turma).
     * Professores podem ver todos os alunos das suas turmas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Aluno só vê o próprio registro.
     * Professor só vê alunos de turmas que ele gerencia.
     */
    public function view(User $user, User $aluno): bool
    {
        if ($user->isProfessor()) {
            return $this->mesmaTurma($user, $aluno);
        }

        return $user->id === $aluno->id;
    }

    /**
     * Professor pode criar/registrar alunos em suas turmas.
     */
    public function create(User $user): bool
    {
        return $user->isProfessor();
    }

    /**
     * Aluno só edita o próprio registro.
     * Professor edita alunos da sua turma.
     */
    public function update(User $user, User $aluno): bool
    {
        if ($user->isProfessor()) {
            return $this->mesmaTurma($user, $aluno);
        }

        return $user->id === $aluno->id;
    }

    /**
     * Só professor pode excluir, e apenas alunos da sua turma.
     */
    public function delete(User $user, User $aluno): bool
    {
        if (! $user->isProfessor()) {
            return false;
        }

        return $this->mesmaTurma($user, $aluno);
    }

    /**
     * Verifica se professor e aluno compartilham ao menos uma turma.
     */
    private function mesmaTurma(User $professor, User $aluno): bool
    {
        $turmasProfessor = $professor->turmas()->pluck('turmas.id');

        return $aluno->turmas()->whereIn('turmas.id', $turmasProfessor)->exists();
    }
}
