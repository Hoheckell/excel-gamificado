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
                'papel' => $index === 0 ? 'arquiteto' : 'auditor',
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

        $this->actingAs($members[0])->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $missao->id,
            'papeis' => [
                $members[0]->id => 'arquiteto',
                $members[1]->id => 'auditor',
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipe_missao_user', [
            'missao_id' => $missao->id,
            'user_id' => $members[0]->id,
            'papel' => 'arquiteto',
            'status' => 'em_andamento',
        ]);
        $this->assertDatabaseHas('equipe_missao_user', [
            'missao_id' => $missao->id,
            'user_id' => $members[1]->id,
            'papel' => 'auditor',
            'status' => 'em_andamento',
        ]);
    }

    public function test_starting_requires_a_role_for_every_active_member(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);

        $this->actingAs($members[0])->from(route('equipes.index'))->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $this->mission(1)->id,
            'papeis' => [$members[0]->id => 'arquiteto'],
        ])->assertRedirect(route('equipes.index'))->assertSessionHasErrors('papeis');

        $this->assertDatabaseCount('equipe_missao_user', 0);
    }

    public function test_member_cannot_repeat_role_from_immediately_previous_mission(): void
    {
        [$turma, $equipe, $members] = $this->teamWithMembers(2);
        $previous = $this->mission(1);
        $current = $this->mission(2);

        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $previous->id,
            'user_id' => $members[0]->id,
            'papel' => 'arquiteto',
        ]);
        EquipeMissaoUser::create([
            'equipe_id' => $equipe->id,
            'missao_id' => $previous->id,
            'user_id' => $members[1]->id,
            'papel' => 'auditor',
        ]);

        $this->actingAs($members[0])->from(route('equipes.index'))->post(route('missoes.iniciar'), [
            'equipe_id' => $equipe->id,
            'missao_id' => $current->id,
            'papeis' => [
                $members[0]->id => 'arquiteto',
                $members[1]->id => 'designer',
            ],
        ])->assertRedirect(route('equipes.index'))->assertSessionHasErrors('papeis');

        $this->assertDatabaseMissing('equipe_missao_user', ['missao_id' => $current->id]);
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

    private function mission(int $order, int $points = 100): Missao
    {
        return Missao::create([
            'titulo' => "Missão {$order}",
            'ordem' => $order,
            'descricao' => 'Descrição da missão',
            'pontuacao' => $points,
        ]);
    }
}
