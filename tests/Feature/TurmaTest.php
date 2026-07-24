<?php

namespace Tests\Feature;

use App\Models\Equipe;
use App\Models\EquipeMissao;
use App\Models\Missao;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_concluir_turma_apaga_anexos_e_substitui_downloads_por_aviso(): void
    {
        Storage::fake('local');
        $professor = User::factory()->create(['tipo' => 'professor']);
        $aluno = User::factory()->create(['tipo' => 'aluno']);
        $turma = Turma::create([
            'codigo' => 'FIM001',
            'descricao' => 'Turma a concluir',
            'dt_inicio' => now()->subMonth(),
            'dt_fim' => now()->toDateString(),
        ]);
        $turma->users()->attach([$professor->id, $aluno->id]);
        $equipe = Equipe::create([
            'turma_id' => $turma->id,
            'nome' => 'Equipe final',
            'pontuacao' => 0,
        ]);
        $aluno->update(['equipe_id' => $equipe->id]);
        $missao = Missao::create([
            'titulo' => 'Missão final',
            'ordem' => 1,
            'descricao' => '<p>Entrega final</p>',
            'pontuacao' => 100,
            'permite_anexo' => true,
        ]);
        $equipe->missoes()->attach($missao);
        $entrega = EquipeMissao::where('equipe_id', $equipe->id)
            ->where('missao_id', $missao->id)
            ->firstOrFail();
        $entrega->update([
            'anexo_path' => 'anexos-missoes/equipe.xlsx',
            'anexo_nome_original' => 'equipe.xlsx',
        ]);
        Storage::disk('local')->put($entrega->anexo_path, 'planilha');
        $anexoMissao = $missao->anexos()->create([
            'path' => 'anexos-missoes/material.pdf',
            'nome_original' => 'material.pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 8,
        ]);
        Storage::disk('local')->put($anexoMissao->path, 'material');

        $this->actingAs($professor)
            ->post(route('turmas.concluir', $turma))
            ->assertRedirect(route('turmas.show', $turma))
            ->assertSessionHasNoErrors();

        $this->assertNotNull($turma->fresh()->concluida_em);
        $this->assertNotNull($entrega->fresh()->anexo_removido_em);
        $this->assertNotNull($anexoMissao->fresh()->removido_em);
        Storage::disk('local')->assertMissing($entrega->anexo_path);
        Storage::disk('local')->assertMissing($anexoMissao->path);

        $aviso = 'arquivo não existe porque a turma foi concluída.';
        $this->actingAs($professor)
            ->get(route('missoes.anexo', $entrega))
            ->assertStatus(410)
            ->assertSeeText($aviso);
        $this->actingAs($professor)
            ->get(route('missoes.recursos.download', [$missao, $anexoMissao]))
            ->assertStatus(410)
            ->assertSeeText($aviso);
        $this->actingAs($professor)
            ->get(route('missoes.show', $missao))
            ->assertOk()
            ->assertSeeText($aviso)
            ->assertDontSee(route('missoes.recursos.download', [$missao, $anexoMissao]), false);
        $this->actingAs($professor)
            ->get(route('equipes.index', ['turma_id' => $turma->id]))
            ->assertOk()
            ->assertSeeText($aviso)
            ->assertDontSee(route('missoes.anexo', $entrega), false);
    }

    public function test_turma_concluida_nao_aceita_novos_anexos_e_conclusao_e_idempotente(): void
    {
        Storage::fake('local');
        $professor = User::factory()->create(['tipo' => 'professor']);
        $aluno = User::factory()->create(['tipo' => 'aluno']);
        $turma = Turma::create([
            'codigo' => 'FIM002',
            'descricao' => 'Turma concluída',
            'dt_inicio' => now()->subMonth(),
            'dt_fim' => now()->toDateString(),
        ]);
        $turma->users()->attach([$professor->id, $aluno->id]);
        $equipe = Equipe::create(['turma_id' => $turma->id, 'nome' => 'Equipe', 'pontuacao' => 0]);
        $aluno->update(['equipe_id' => $equipe->id]);
        $missao = Missao::create([
            'titulo' => 'Missão',
            'ordem' => 1,
            'descricao' => 'Descrição',
            'pontuacao' => 100,
            'permite_anexo' => true,
        ]);
        $equipe->missoes()->attach($missao);

        $this->actingAs($professor)->post(route('turmas.concluir', $turma))->assertSessionHasNoErrors();
        $concluidaEm = $turma->fresh()->concluida_em;
        $this->actingAs($professor)->post(route('turmas.concluir', $turma))->assertSessionHasNoErrors();
        $this->assertTrue($concluidaEm->equalTo($turma->fresh()->concluida_em));

        $this->actingAs($aluno)->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'anexo' => UploadedFile::fake()->create('novo.xlsx', 10),
        ])->assertSessionHasErrors('entrega');

        $this->assertDatabaseMissing('equipes_missoes', [
            'equipe_id' => $equipe->id,
            'anexo_nome_original' => 'novo.xlsx',
        ]);
    }

    public function test_aluno_nao_pode_concluir_turma(): void
    {
        $aluno = User::factory()->create(['tipo' => 'aluno']);
        $turma = Turma::create([
            'codigo' => 'FIM003',
            'descricao' => 'Turma protegida',
            'dt_inicio' => now()->subMonth(),
            'dt_fim' => now()->toDateString(),
        ]);
        $turma->users()->attach($aluno);

        $this->actingAs($aluno)
            ->post(route('turmas.concluir', $turma))
            ->assertForbidden();

        $this->assertNull($turma->fresh()->concluida_em);
    }
}
