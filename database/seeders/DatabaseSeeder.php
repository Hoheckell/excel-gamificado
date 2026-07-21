<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Categoria;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['nome' => 'Crescimento', 'pt_classificacao' => 0, 'descricao' => 'Superação e trabalho em equipe', 'titulo_certificado' => 'Superação e Trabalho em Equipe', 'cor' => '#64748b'],
            ['nome' => 'Bronze', 'pt_classificacao' => 300, 'descricao' => 'Organização, design e gráficos', 'titulo_certificado' => 'Especialista em Formatação', 'cor' => '#b45309'],
            ['nome' => 'Prata', 'pt_classificacao' => 380, 'descricao' => 'Funções essenciais dominadas', 'titulo_certificado' => 'Analista de Planilhas', 'cor' => '#64748b'],
            ['nome' => 'Ouro', 'pt_classificacao' => 450, 'descricao' => 'Fórmulas, lógica e visual profissional', 'titulo_certificado' => 'Mestre dos Dados', 'cor' => '#ca8a04'],
        ] as $categoria) {
            Categoria::updateOrCreate(['nome' => $categoria['nome']], $categoria);
        }

        foreach ([
            ['nome' => 'Zero Mouse', 'icone' => '⌨️', 'descricao' => 'Dominou os atalhos de teclado.', 'pontos_bonus' => 15],
            ['nome' => 'Código Limpo', 'icone' => '🧹', 'descricao' => 'Criou fórmulas claras e sem erros.', 'pontos_bonus' => 15],
            ['nome' => 'Visual Executivo', 'icone' => '🎨', 'descricao' => 'Entregou uma planilha com acabamento profissional.', 'pontos_bonus' => 15],
            ['nome' => 'Salva-Vidas', 'icone' => '🤝', 'descricao' => 'Ajudou outra equipe sem assumir o computador.', 'pontos_bonus' => 15],
        ] as $badge) {
            Badge::updateOrCreate(['nome' => $badge['nome']], $badge);
        }

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
            'autorizado' => false,
        ]);

        foreach ($alunos as $aluno) {
            $turma->users()->attach($aluno->id);
        }

        $this->command->info("Turma {$turma->codigo} criada com 20 alunos e 1 professor.");
    }
}
