<?php

namespace Tests\Feature;

use App\Models\Turma;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurmaTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_exibe_quantidade_de_professores_da_turma(): void
    {
        $professor = User::factory()->create(['tipo' => 'professor']);
        $aluno = User::factory()->create(['tipo' => 'aluno']);
        $turma = Turma::create([
            'codigo' => 'TURMA-001',
            'descricao' => 'Turma de teste',
            'dt_inicio' => now()->toDateString(),
            'dt_fim' => now()->addMonth()->toDateString(),
        ]);

        $turma->users()->attach([$professor->id, $aluno->id]);

        $this->actingAs($professor)
            ->get(route('turmas.index'))
            ->assertOk()
            ->assertViewHas('turmas', function ($turmas) use ($turma) {
                $turmaListada = $turmas->firstWhere('id', $turma->id);

                return $turmaListada !== null
                    && $turmaListada->professores_count === 1;
            });
    }

    public function test_show_exibe_iniciais_com_caracteres_acentuados(): void
    {
        $professor = User::factory()->create([
            'name' => 'Sérgio',
            'tipo' => 'professor',
        ]);
        $turma = Turma::create([
            'codigo' => 'TURMA-002',
            'descricao' => 'Turma com acentuação',
            'dt_inicio' => now()->toDateString(),
            'dt_fim' => now()->addMonth()->toDateString(),
        ]);

        $turma->users()->attach($professor);

        $this->actingAs($professor)
            ->get(route('turmas.show', $turma))
            ->assertOk()
            ->assertSee('Sé')
            ->assertSee('bg-excel-dark text-white', false)
            ->assertSee('Prof');
    }
}
