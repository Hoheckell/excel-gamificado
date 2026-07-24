<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Categoria;
use App\Models\Equipe;
use App\Models\EquipeMissaoUser;
use App\Models\Missao;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_xp_total_combines_base_points_mission_scores_and_badges_without_multiplying_team_score(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2, 10);
        $missao = $this->mission(1, 100);
        $badge = Badge::firstOrFail();

        foreach ($members as $index => $member) {
            EquipeMissaoUser::create([
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'user_id' => $member->id,
                'status' => 'concluida',
                'pontuacao_obtida' => $index === 0 ? 80 : 90,
            ]);
        }

        $equipe->badges()->attach($badge);

        $this->assertSame(115, $equipe->fresh()->xp_total);
    }

    public function test_category_uses_highest_threshold_reached(): void
    {
        Categoria::create(['nome' => 'Crescimento', 'pt_classificacao' => 0]);
        Categoria::create(['nome' => 'Bronze', 'pt_classificacao' => 300]);
        Categoria::create(['nome' => 'Prata', 'pt_classificacao' => 380]);
        Categoria::create(['nome' => 'Ouro', 'pt_classificacao' => 450]);

        $this->assertSame('Prata', Categoria::paraPontuacao(449)->nome);
        $this->assertSame('Ouro', Categoria::paraPontuacao(450)->nome);
    }

    public function test_professor_can_grant_a_badge_only_once_and_remove_it(): void
    {
        [$turma, $equipe] = $this->teamWithMembers();
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $badge = Badge::firstOrFail();

        $this->actingAs($professor)
            ->post(route('equipes.badges.store', $equipe), ['badge_id' => $badge->id])
            ->assertSessionHasNoErrors();

        $this->actingAs($professor)
            ->post(route('equipes.badges.store', $equipe), ['badge_id' => $badge->id])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('equipe_badge', 1);

        $this->actingAs($professor)
            ->delete(route('equipes.badges.destroy', [$equipe, $badge]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('equipe_badge', 0);
    }

    public function test_starting_a_mission_assigns_roles_and_starts_every_active_member(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1);
        $equipe->missoes()->attach($missao);

        $this->actingAs($members[0])->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'perfis' => [
                $members[0]->id => 'tecnico',
                $members[1]->id => 'executivo',
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipe_missao_user', [
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'em_andamento',
        ]);
        $progressoTecnico = EquipeMissaoUser::where('missao_id', $missao->id)
            ->where('user_id', $members[0]->id)->firstOrFail();
        $progressoExecutivo = EquipeMissaoUser::where('missao_id', $missao->id)
            ->where('user_id', $members[1]->id)->firstOrFail();
        $this->assertEqualsCanonicalizing(['arquiteto', 'auditor'], $progressoTecnico->papeis->pluck('papel')->all());
        $this->assertEqualsCanonicalizing(['designer', 'gestor'], $progressoExecutivo->papeis->pluck('papel')->all());
        $this->assertDatabaseHas('equipes_missoes', [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'tempo_extra_minutos' => 5,
        ]);
    }

    public function test_starting_requires_a_profile_for_every_present_member(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1);
        $equipe->missoes()->attach($missao);

        $this->actingAs($members[0])->from(route('equipes.index'))->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'perfis' => [$members[0]->id => 'tecnico'],
        ])->assertRedirect(route('equipes.index'))->assertSessionHasErrors('perfis');

        $this->assertDatabaseCount('equipe_missao_user', 0);
    }

    public function test_team_cannot_repeat_the_same_multiclass_distribution(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $previous = $this->mission(1);
        $current = $this->mission(2);

        $equipe->missoes()->attach([$previous->id, $current->id]);
        $progressoTecnico = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $previous->id,
            'user_id' => $members[0]->id,
        ]);
        $progressoTecnico->papeis()->createMany([['papel' => 'arquiteto'], ['papel' => 'auditor']]);
        $progressoExecutivo = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $previous->id,
            'user_id' => $members[1]->id,
        ]);
        $progressoExecutivo->papeis()->createMany([['papel' => 'designer'], ['papel' => 'gestor']]);

        $this->actingAs($members[0])->from(route('equipes.index'))->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $current->id,
            'perfis' => [
                $members[0]->id => 'tecnico',
                $members[1]->id => 'executivo',
            ],
        ])->assertRedirect(route('equipes.index'))->assertSessionHasErrors('perfis');

        $this->assertDatabaseMissing('equipe_missao_user', ['missao_id' => $current->id]);
    }

    public function test_team_cannot_send_mission_submission_before_every_member_finishes(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1, 100, true, true);
        $equipe->missoes()->attach($missao);
        foreach ($members as $index => $member) {
            EquipeMissaoUser::create([
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'user_id' => $member->id,
                'status' => $index ? 'em_andamento' : 'concluida',
            ]);
        }

        $this->actingAs($members[0])->from(route('equipes.index'))->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Resposta antecipada',
        ])->assertRedirect(route('equipes.index'))->assertSessionHasErrors('entrega');

        $this->assertDatabaseMissing('equipes_missoes', [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Resposta antecipada',
        ]);
    }

    public function test_team_can_send_text_and_attachment_after_every_member_finishes(): void
    {
        Storage::fake('local');
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1, 100, true, true);
        $equipe->missoes()->attach($missao);
        foreach ($members as $index => $member) {
            EquipeMissaoUser::create([
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'user_id' => $member->id,
                'status' => 'concluida',
            ]);
        }

        $this->actingAs($members[0])->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Planilha finalizada e conferida.',
            'anexo' => UploadedFile::fake()->create('resultado.xlsx', 100),
        ])->assertSessionHasNoErrors();

        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;
        $this->assertSame('Planilha finalizada e conferida.', $entrega->resposta);
        $this->assertSame('resultado.xlsx', $entrega->anexo_nome_original);
        Storage::disk('local')->assertExists($entrega->anexo_path);
    }

    public function test_professor_can_request_attachment_resubmission_with_feedback_without_changing_score(): void
    {
        Storage::fake('local');
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/original.xlsx',
            'anexo_nome_original' => 'original.xlsx',
        ]);
        Storage::disk('local')->put('anexos-missoes/original.xlsx', 'original');
        foreach ($members as $member) {
            EquipeMissaoUser::create([
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'user_id' => $member->id,
                'status' => 'concluida',
                'pontuacao_obtida' => 80,
            ]);
        }
        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;

        $this->actingAs($professor)->post(route('missoes.solicitarReenvio', $entrega->id), [
            'feedback_reenvio' => 'Corrijam as referências absolutas e reenviem a planilha.',
        ])->assertSessionHasNoErrors();

        $entrega->refresh();
        $this->assertSame('Corrijam as referências absolutas e reenviem a planilha.', $entrega->feedback_reenvio);
        $this->assertNotNull($entrega->reenvio_solicitado_em);
        $this->assertNull($entrega->reenvio_entregue_em);
        $this->assertSame([80, 80], EquipeMissaoUser::where('missao_id', $missao->id)->pluck('pontuacao_obtida')->all());
    }

    public function test_team_resubmits_requested_attachment_and_original_score_is_preserved(): void
    {
        Storage::fake('local');
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/original.xlsx',
            'anexo_nome_original' => 'original.xlsx',
            'feedback_reenvio' => 'Revise a fórmula.',
            'reenvio_solicitado_em' => now(),
        ]);
        Storage::disk('local')->put('anexos-missoes/original.xlsx', 'original');
        $registro = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
            'pontuacao_obtida' => 75,
        ]);

        $this->actingAs($members[0])->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'anexo' => UploadedFile::fake()->create('corrigido.xlsx', 100),
        ])->assertSessionHasNoErrors();

        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;
        $this->assertSame('corrigido.xlsx', $entrega->anexo_nome_original);
        $this->assertNotNull($entrega->reenvio_entregue_em);
        $this->assertSame(75, $registro->fresh()->pontuacao_obtida);
        Storage::disk('local')->assertMissing('anexos-missoes/original.xlsx');
        Storage::disk('local')->assertExists($entrega->anexo_path);
    }

    public function test_attachment_resubmission_requires_professor_feedback(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/original.xlsx',
            'anexo_nome_original' => 'original.xlsx',
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);
        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;

        $this->actingAs($professor)->post(route('missoes.solicitarReenvio', $entrega->id), [
            'feedback_reenvio' => '',
        ])->assertSessionHasErrors('feedback_reenvio');

        $this->assertNull($entrega->fresh()->reenvio_solicitado_em);
    }

    public function test_team_can_edit_text_response_only_before_professor_assessment(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $missao = $this->mission(1, 100, true);
        $equipe->missoes()->attach($missao, ['resposta' => 'Primeira resposta']);
        $registro = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);

        $this->actingAs($members[0])->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Resposta revisada antes da avaliação',
        ])->assertSessionHasNoErrors();

        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;
        $this->assertSame('Resposta revisada antes da avaliação', $entrega->resposta);

        $registro->update(['pontuacao_obtida' => 80]);
        $this->actingAs($members[0])->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Tentativa depois da avaliação',
        ])->assertSessionHasErrors('resposta');

        $this->assertSame('Resposta revisada antes da avaliação', $entrega->fresh()->resposta);
        $this->actingAs($members[0])->get(route('equipes.index'))
            ->assertOk()
            ->assertDontSee('Editar resposta textual');
    }

    public function test_professor_sees_resubmitted_attachment_as_ready_for_reassessment(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/corrigido.xlsx',
            'anexo_nome_original' => 'corrigido.xlsx',
            'feedback_reenvio' => 'Revise a fórmula.',
            'reenvio_solicitado_em' => now()->subMinute(),
            'reenvio_entregue_em' => now(),
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
            'pontuacao_obtida' => 75,
        ]);

        $this->actingAs($professor)->get(route('equipes.index'))
            ->assertOk()
            ->assertSee('Novo anexo pronto para reavaliação')
            ->assertSee('Revisar avaliação');
    }

    public function test_student_cannot_request_attachment_resubmission(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/original.xlsx',
            'anexo_nome_original' => 'original.xlsx',
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);
        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;

        $this->actingAs($members[0])->post(route('missoes.solicitarReenvio', $entrega->id), [
            'feedback_reenvio' => 'Tentativa sem autorização.',
        ])->assertForbidden();

        $this->assertNull($entrega->fresh()->reenvio_solicitado_em);
    }

    public function test_professor_cannot_request_resubmission_before_team_finishes_mission(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/original.xlsx',
            'anexo_nome_original' => 'original.xlsx',
        ]);
        foreach ($members as $index => $member) {
            EquipeMissaoUser::create([
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'user_id' => $member->id,
                'status' => $index === 0 ? 'concluida' : 'em_andamento',
            ]);
        }
        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;

        $this->actingAs($professor)->post(route('missoes.solicitarReenvio', $entrega->id), [
            'feedback_reenvio' => 'Corrija a planilha.',
        ])->assertSessionHasErrors('reenvio');

        $this->assertNull($entrega->fresh()->reenvio_solicitado_em);
    }

    public function test_requested_resubmission_requires_a_new_attachment(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $missao = $this->mission(1, 100, true, true);
        $equipe->missoes()->attach($missao, [
            'resposta' => 'Resposta avaliada.',
            'anexo_path' => 'anexos-missoes/original.xlsx',
            'anexo_nome_original' => 'original.xlsx',
            'feedback_reenvio' => 'Corrija o arquivo.',
            'reenvio_solicitado_em' => now(),
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
            'pontuacao_obtida' => 70,
        ]);

        $this->actingAs($members[0])->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
        ])->assertSessionHasErrors('anexo');

        $entrega = $equipe->missoes()->whereKey($missao->id)->firstOrFail()->pivot;
        $this->assertNull($entrega->reenvio_entregue_em);
        $this->assertSame('Resposta avaliada.', $entrega->resposta);
    }

    public function test_professor_can_reassess_after_attachment_resubmission(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $missao = $this->mission(1, 100, false, true);
        $equipe->missoes()->attach($missao, [
            'anexo_path' => 'anexos-missoes/corrigido.xlsx',
            'anexo_nome_original' => 'corrigido.xlsx',
            'feedback_reenvio' => 'Revise a fórmula.',
            'reenvio_solicitado_em' => now()->subMinute(),
            'reenvio_entregue_em' => now(),
        ]);
        $registro = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
            'pontuacao_obtida' => 70,
            'competencia_formulas' => 'em_desenvolvimento',
            'competencia_qualidade' => 'dominado',
            'competencia_visual' => 'dominado',
            'competencia_colaboracao' => 'dominado',
            'proximo_passo' => 'Corrigir as referências.',
        ]);

        $this->actingAs($professor)->post(route('missoes.pontuar'), [
            'registro_id' => $registro->id,
            'pontuacao' => 90,
            'competencia_formulas' => 'dominado',
            'competencia_qualidade' => 'dominado',
            'competencia_visual' => 'dominado',
            'competencia_colaboracao' => 'dominado',
            'feedback_professor' => 'O novo arquivo corrigiu as referências.',
            'proximo_passo' => '',
        ])->assertSessionHasNoErrors();

        $registro->refresh();
        $this->assertSame(90, $registro->pontuacao_obtida);
        $this->assertSame('dominado', $registro->competencia_formulas);
        $this->assertSame('O novo arquivo corrigiu as referências.', $registro->feedback_professor);
    }

    public function test_absence_reported_before_start_is_preserved_and_role_is_only_required_for_present_members(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(3);
        $missao = $this->mission(1);
        $equipe->missoes()->attach($missao);

        $this->actingAs($members[0])->post(route('missoes.comunicarFalta'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[2]->id,
        ])->assertSessionHasNoErrors();

        $this->actingAs($members[0])->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'perfis' => [
                $members[0]->id => 'tecnico',
                $members[1]->id => 'executivo',
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipe_missao_user', [
            'missao_id' => $missao->id,
            'user_id' => $members[2]->id,
            'status' => 'ausente',
        ]);
    }

    public function test_absent_member_does_not_block_team_submission_and_absence_cannot_be_replaced(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1, 100, true);
        $equipe->missoes()->attach($missao);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);

        $payload = [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[1]->id,
        ];
        $this->actingAs($members[0])->post(route('missoes.comunicarFalta'), $payload)
            ->assertSessionHasNoErrors();

        $this->actingAs($members[0])->post(route('missoes.entregar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Entrega da equipe presente.',
        ])->assertSessionHasNoErrors();

        $this->actingAs($members[0])->from(route('equipes.index'))
            ->post(route('missoes.comunicarFalta'), $payload)
            ->assertRedirect(route('equipes.index'))
            ->assertSessionHasErrors('falta');
        $this->assertDatabaseHas('equipe_missao_user', [
            'missao_id' => $missao->id,
            'user_id' => $members[1]->id,
            'status' => 'ausente',
        ]);
        $this->assertDatabaseHas('equipes_missoes', [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'resposta' => 'Entrega da equipe presente.',
        ]);
    }

    public function test_missions_screen_shows_absence_action_to_student_after_mission_starts(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1);
        $equipe->missoes()->attach($missao);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'em_andamento',
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[1]->id,
            'status' => 'em_andamento',
        ]);

        $this->actingAs($members[0])->get(route('missoes.index'))
            ->assertOk()
            ->assertSee('Comunicar falta')
            ->assertSee($members[1]->name);
    }

    public function test_professor_can_open_and_submit_mission_edit_form(): void
    {
        $professor = User::factory()->create(['tipo' => 'professor']);
        $missao = $this->mission(1);

        $this->actingAs($professor)->get(route('missoes.edit', $missao))
            ->assertOk()
            ->assertSee('Editar Missão')
            ->assertSee($missao->titulo);

        $this->actingAs($professor)->put(route('missoes.update', $missao), [
            'titulo' => 'Missão atualizada',
            'ordem' => 2,
            'descricao' => 'Nova descrição',
            'pontuacao' => 150,
            'permite_resposta' => '1',
        ])->assertRedirect(route('missoes.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('missoes', [
            'id' => $missao->id,
            'titulo' => 'Missão atualizada',
            'permite_resposta' => true,
        ]);
    }

    public function test_professor_can_create_mission_with_safe_html_url_and_unlimited_private_attachments(): void
    {
        Storage::fake('local');
        $professor = User::factory()->create(['tipo' => 'professor']);

        $response = $this->actingAs($professor)->post(route('missoes.store'), [
            'titulo' => 'Missão com materiais',
            'ordem' => 3,
            'descricao' => '<h2>Planilha</h2><p>Use <strong>PROCV</strong>.</p><script>alert(1)</script><a href="javascript:alert(2)">Perigo</a>',
            'url' => 'https://example.com/material',
            'pontuacao' => 100,
            'anexos' => [
                UploadedFile::fake()->createWithContent(
                    'roteiro.jpg',
                    base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////2wBDAf//////////////////////////////////////////////////////////////////////////////////////wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAf/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABBQJ//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAwEBPwF//8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAgBAgEBPwF//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQAGPwJ//8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPyF//9oADAMBAAIAAwAAABD/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oACAEDAQE/EB//xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oACAECAQE/EB//xAAUEAEAAAAAAAAAAAAAAAAAAAAA/9oACAEBAAE/EB//2Q==')
                ),
                UploadedFile::fake()->create('dados.csv', 100, 'text/csv'),
                UploadedFile::fake()->create('guia.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('missoes.index'))->assertSessionHasNoErrors();
        $missao = Missao::where('titulo', 'Missão com materiais')->firstOrFail();

        $this->assertStringContainsString('<strong>PROCV</strong>', $missao->descricao);
        $this->assertStringNotContainsString('<script', $missao->descricao);
        $this->assertStringNotContainsString('javascript:', $missao->descricao);
        $this->assertSame('https://example.com/material', $missao->url);
        $this->assertCount(3, $missao->anexos);
        $missao->anexos->each(fn ($anexo) => Storage::disk('local')->assertExists($anexo->path));

        $this->actingAs($professor)->get(route('missoes.show', $missao))
            ->assertOk()
            ->assertSee('<strong>PROCV</strong>', false)
            ->assertDontSee('alert(1)')
            ->assertDontSee('javascript:alert(2)')
            ->assertSee('Abrir URL de apoio')
            ->assertSee('roteiro.jpg');

        $this->actingAs($professor)->get(route('missoes.index'))
            ->assertOk()
            ->assertSee('<strong>PROCV</strong>', false)
            ->assertDontSee('alert(1)')
            ->assertDontSee('javascript:alert(2)');
    }

    public function test_mission_rejects_unsafe_url_disallowed_extension_and_attachment_over_three_megabytes(): void
    {
        Storage::fake('local');
        $professor = User::factory()->create(['tipo' => 'professor']);
        $base = [
            'titulo' => 'Missão inválida',
            'ordem' => 4,
            'descricao' => '<p>Teste</p>',
            'pontuacao' => 100,
        ];

        $this->actingAs($professor)->post(route('missoes.store'), $base + [
            'url' => 'javascript:alert(1)',
            'anexos' => [UploadedFile::fake()->create('programa.exe', 10, 'application/octet-stream')],
        ])->assertSessionHasErrors(['url', 'anexos.0']);

        $this->actingAs($professor)->post(route('missoes.store'), $base + [
            'anexos' => [UploadedFile::fake()->create('grande.pdf', 3073, 'application/pdf')],
        ])->assertSessionHasErrors('anexos.0');

        $this->assertDatabaseMissing('missoes', ['titulo' => 'Missão inválida']);
    }

    public function test_professor_can_remove_a_mission_attachment_and_file_is_deleted(): void
    {
        Storage::fake('local');
        $professor = User::factory()->create(['tipo' => 'professor']);
        $missao = $this->mission(1);
        Storage::disk('local')->put('anexos-missoes/1/arquivo.pdf', 'pdf');
        $anexo = $missao->anexos()->create([
            'path' => 'anexos-missoes/1/arquivo.pdf',
            'nome_original' => 'arquivo.pdf',
            'mime_type' => 'application/pdf',
            'tamanho' => 3,
        ]);

        $this->actingAs($professor)->put(route('missoes.update', $missao), [
            'titulo' => $missao->titulo,
            'ordem' => $missao->ordem,
            'descricao' => $missao->descricao,
            'pontuacao' => $missao->pontuacao,
            'remover_anexos' => [$anexo->id],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('missao_anexos', ['id' => $anexo->id]);
        Storage::disk('local')->assertMissing($anexo->path);
    }

    public function test_placar_uses_xp_average_and_keeps_350_in_tense_state(): void
    {
        [$turma] = $this->teamWithMembers(1, 350);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);

        $this->actingAs($professor)
            ->get(route('placar.index', ['turma_id' => $turma->id]))
            ->assertOk()
            ->assertViewHas('humorChefe', fn (array $humor) => $humor['estado'] === 'tenso');
    }

    public function test_professor_can_save_learning_rubric_feedback_and_next_step_without_changing_xp_contract(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1, 10);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $missao = $this->mission(1, 100);
        $registro = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);

        $this->actingAs($professor)->post(route('missoes.pontuar'), [
            'registro_id' => $registro->id,
            'pontuacao' => 80,
            'competencia_formulas' => 'em_desenvolvimento',
            'competencia_qualidade' => 'dominado',
            'competencia_visual' => 'dominado',
            'competencia_colaboracao' => 'dominado',
            'feedback_professor' => 'A fórmula funciona e pode ficar mais legível.',
            'proximo_passo' => 'Pratique referências absolutas na próxima planilha.',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipe_missao_user', [
            'id' => $registro->id,
            'pontuacao_obtida' => 80,
            'competencia_formulas' => 'em_desenvolvimento',
            'feedback_professor' => 'A fórmula funciona e pode ficar mais legível.',
            'proximo_passo' => 'Pratique referências absolutas na próxima planilha.',
        ]);
        $this->assertSame(90, $equipe->fresh()->xp_total);
    }

    public function test_assessment_requires_valid_levels_and_next_step_when_learning_is_in_progress(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers();
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $registro = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $this->mission(1)->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);
        $payload = [
            'registro_id' => $registro->id,
            'pontuacao' => 70,
            'competencia_formulas' => 'em_desenvolvimento',
            'competencia_qualidade' => 'dominado',
            'competencia_visual' => 'dominado',
            'competencia_colaboracao' => 'dominado',
        ];

        $this->actingAs($professor)->from(route('equipes.index'))
            ->post(route('missoes.pontuar'), $payload)
            ->assertRedirect(route('equipes.index'))
            ->assertSessionHasErrors('proximo_passo');

        $payload['competencia_formulas'] = 'perfeito';
        $payload['proximo_passo'] = 'Revisar a fórmula.';
        $this->actingAs($professor)->from(route('equipes.index'))
            ->post(route('missoes.pontuar'), $payload)
            ->assertSessionHasErrors('competencia_formulas');
    }

    public function test_student_cannot_assess_mission_progress(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers();
        $registro = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $this->mission(1)->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);

        $this->actingAs($members[0])->post(route('missoes.pontuar'), [
            'registro_id' => $registro->id,
            'pontuacao' => 100,
            'competencia_formulas' => 'dominado',
            'competencia_qualidade' => 'dominado',
            'competencia_visual' => 'dominado',
            'competencia_colaboracao' => 'dominado',
        ])->assertForbidden();

        $this->assertNull($registro->fresh()->pontuacao_obtida);
    }

    public function test_student_dashboard_shows_personal_journey_and_never_another_members_feedback(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $missao = $this->mission(1);
        $equipe->missoes()->attach($missao);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
            'pontuacao_obtida' => 80,
            'competencia_formulas' => 'em_desenvolvimento',
            'competencia_qualidade' => 'dominado',
            'competencia_visual' => 'dominado',
            'competencia_colaboracao' => 'dominado',
            'feedback_professor' => 'Feedback pessoal visível.',
            'proximo_passo' => 'Revisar referências absolutas.',
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[1]->id,
            'status' => 'concluida',
            'pontuacao_obtida' => 90,
            'feedback_professor' => 'Feedback secreto do colega.',
        ]);

        $this->actingAs($members[0])->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Minha Jornada')
            ->assertSee('Feedback pessoal visível.')
            ->assertSee('Revisar referências absolutas.')
            ->assertDontSee('Feedback secreto do colega.');
    }

    public function test_student_dashboard_guides_empty_pending_and_in_progress_states(): void
    {
        $studentWithoutTeam = User::factory()->create(['equipe_id' => null]);
        $this->actingAs($studentWithoutTeam)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sua jornada está quase pronta');

        [$turma, $equipe, $members] = $this->teamWithMembers();
        $missao = $this->mission(1);
        $equipe->missoes()->attach($missao);

        $this->actingAs($members[0])->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Combine os papéis com sua equipe e inicie a missão.')
            ->assertSee('A definir com a equipe');

        $progresso = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'status' => 'em_andamento',
        ]);
        $progresso->papeis()->create(['papel' => 'auditor']);

        $this->actingAs($members[0])->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pratique sua função e finalize sua participação quando terminar.')
            ->assertSee('Auditor de Qualidade');
    }

    public function test_multiclass_profiles_cover_all_roles_for_one_three_and_four_present_members(): void
    {
        foreach ([
            1 => ['senior'],
            3 => ['arquiteto', 'designer', 'controle'],
            4 => ['arquiteto', 'designer', 'auditor', 'gestor'],
        ] as $count => $profiles) {
            [$turma, $equipe, $members] = $this->teamWithMembers($count);
            $missao = $this->mission($count);
            $equipe->missoes()->attach($missao);

            $this->actingAs($members[0])->post(route('missoes.iniciar'), [
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'perfis' => $members->values()->mapWithKeys(
                    fn ($member, $index) => [$member->id => $profiles[$index]]
                )->all(),
            ])->assertSessionHasNoErrors();

            $roles = EquipeMissaoUser::where('equipe_id', $equipe->id)
                ->where('missao_id', $missao->id)
                ->with('papeis')
                ->get()
                ->flatMap->papeis
                ->pluck('papel');
            $this->assertEqualsCanonicalizing(
                ['arquiteto', 'auditor', 'designer', 'gestor'],
                $roles->all()
            );
            $this->assertDatabaseHas('equipes_missoes', [
                'equipe_id' => $equipe->id,
                'missao_id' => $missao->id,
                'tempo_extra_minutos' => $count <= 3 ? 5 : 0,
            ]);
        }
    }

    public function test_changed_attendance_requires_a_new_primary_role_when_an_alternative_exists(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(3);
        $previous = $this->mission(1);
        $current = $this->mission(2);
        $equipe->missoes()->attach([$previous->id, $current->id]);

        foreach ([
            [$members[0], ['arquiteto']],
            [$members[1], ['designer']],
            [$members[2], ['auditor', 'gestor']],
        ] as [$member, $roles]) {
            $progress = EquipeMissaoUser::create([
                'equipe_id' => $equipe->id,
                'missao_id' => $previous->id,
                'user_id' => $member->id,
                'status' => 'concluida',
            ]);
            $progress->papeis()->createMany(collect($roles)->map(fn ($role) => ['papel' => $role])->all());
        }
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $current->id,
            'user_id' => $members[2]->id,
            'status' => 'ausente',
        ]);

        $this->actingAs($members[0])->from(route('equipes.index'))->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $current->id,
            'perfis' => [
                $members[0]->id => 'tecnico',
                $members[1]->id => 'executivo',
            ],
        ])->assertSessionHasErrors('perfis');

        $this->actingAs($members[0])->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $current->id,
            'perfis' => [
                $members[0]->id => 'executivo',
                $members[1]->id => 'tecnico',
            ],
        ])->assertSessionHasNoErrors();
    }

    public function test_solo_consultant_can_repeat_senior_profile_because_no_rotation_is_possible(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1);
        $previous = $this->mission(1);
        $current = $this->mission(2);
        $equipe->missoes()->attach([$previous->id, $current->id]);
        $progress = EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $previous->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);
        $progress->papeis()->createMany(
            collect(array_keys(EquipeMissaoUser::PAPEIS))
                ->map(fn ($role) => ['papel' => $role])
                ->all()
        );

        $this->actingAs($members[0])->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $current->id,
            'perfis' => [$members[0]->id => 'senior'],
        ])->assertSessionHasNoErrors();
    }

    public function test_professor_dashboard_suggests_manual_regrouping_only_after_third_mission(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $members->each->update(['autorizado' => true]);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);
        $second = $this->mission(2);
        $third = $this->mission(3);

        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $second->id,
            'user_id' => $members[0]->id,
            'status' => 'concluida',
        ]);
        $this->actingAs($professor)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Sugestão pedagógica de reagrupamento');

        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $third->id,
            'user_id' => $members[0]->id,
            'status' => 'em_andamento',
        ]);
        $this->actingAs($professor)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sugestão pedagógica de reagrupamento')
            ->assertSee($equipe->nome)
            ->assertSee('decisão manual do professor');
    }

    public function test_student_progress_hides_other_teams_and_public_positions_while_professor_keeps_ranking(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(1, 100);
        $outraEquipe = Equipe::create([
            'turma_id' => $turma->id,
            'nome' => 'Equipe que não deve aparecer',
            'pontuacao' => 500,
        ]);
        $professor = User::factory()->create(['tipo' => 'professor']);
        $turma->users()->attach($professor);

        $this->actingAs($members[0])->get(route('placar.index', ['turma_id' => $turma->id]))
            ->assertOk()
            ->assertSee('Progresso da Minha Equipe')
            ->assertSee($equipe->nome)
            ->assertDontSee($outraEquipe->nome)
            ->assertDontSee('🥇');

        $this->actingAs($professor)->get(route('placar.index', ['turma_id' => $turma->id]))
            ->assertOk()
            ->assertSee('Placar Geral')
            ->assertSee($equipe->nome)
            ->assertSee($outraEquipe->nome)
            ->assertSee('🥇');
    }

    private function teamWithMembers(int $count = 1, int $basePoints = 0): array
    {
        $turma = Turma::create([
            'codigo' => fake()->unique()->bothify('??####'),
            'descricao' => 'Excel Básico',
            'dt_inicio' => now()->subDay(),
            'dt_fim' => now()->addMonth(),
        ]);
        $equipe = Equipe::create([
            'turma_id' => $turma->id,
            'nome' => 'Consultoria Teste',
            'pontuacao' => $basePoints,
        ]);
        $members = User::factory($count)->create(['equipe_id' => $equipe->id]);
        $turma->users()->attach($members->pluck('id'));

        return [$turma, $equipe, $members];
    }

    private function mission(int $order, int $points = 100, bool $allowsResponse = false, bool $allowsAttachment = false): Missao
    {
        return Missao::create([
            'titulo' => "Missão {$order}",
            'ordem' => $order,
            'descricao' => 'Descrição da missão',
            'pontuacao' => $points,
            'permite_resposta' => $allowsResponse,
            'permite_anexo' => $allowsAttachment,
        ]);
    }
}
