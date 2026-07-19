<?php

namespace Database\Seeders;

use App\Models\Turma;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $professor = User::factory()->create([
            'name' => 'Professor',
            'email' => 'teste@gmail.com',
            'tipo' => 'professor',
            'autorizado' => true,
        ]);

        $turma = Turma::create([
            'codigo' => 'EXC001',
            'descricao' => 'Excel Básico — Turma Manhã 2026',
            'dt_inicio' => '2026-06-01',
            'dt_fim' => '2026-07-15',
        ]);

        $turma->users()->attach($professor->id);

        $alunos = User::factory(20)->create([
            'tipo' => 'aluno',
            'autorizado' => true,
        ]);

        foreach ($alunos as $aluno) {
            $turma->users()->attach($aluno->id);
        }

        $this->command->info("Turma {$turma->codigo} criada com 20 alunos e 1 professor.");
    }
}
