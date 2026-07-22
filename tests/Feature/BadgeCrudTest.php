<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_professor_pode_fazer_crud_de_badges(): void
    {
        $professor = User::factory()->create(['tipo' => 'professor']);

        $this->actingAs($professor)
            ->get(route('badges.index'))
            ->assertOk()
            ->assertSee('Nova Badge');

        $this->get(route('badges.create'))->assertOk();

        $this->post(route('badges.store'), [
            'nome' => 'Mestre das Fórmulas',
            'icone' => '🧠',
            'descricao' => 'Aplicou fórmulas com precisão.',
            'pontos_bonus' => 20,
        ])->assertRedirect(route('badges.index'));

        $badge = Badge::where('nome', 'Mestre das Fórmulas')->firstOrFail();

        $this->get(route('badges.edit', $badge))->assertOk();

        $this->put(route('badges.update', $badge), [
            'nome' => 'Mestre das Funções',
            'icone' => '🧠',
            'descricao' => 'Aplicou funções com precisão.',
            'pontos_bonus' => 25,
        ])->assertRedirect(route('badges.index'));

        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'nome' => 'Mestre das Funções',
            'pontos_bonus' => 25,
        ]);

        $this->delete(route('badges.destroy', $badge))
            ->assertRedirect(route('badges.index'));

        $this->assertDatabaseMissing('badges', ['id' => $badge->id]);
    }

    public function test_aluno_nao_pode_alterar_catalogo_de_badges(): void
    {
        $aluno = User::factory()->create(['tipo' => 'aluno']);
        $badge = Badge::firstOrFail();

        $this->actingAs($aluno)
            ->get(route('badges.index'))
            ->assertOk()
            ->assertDontSee('Nova Badge');

        $this->get(route('badges.create'))->assertForbidden();

        $this->post(route('badges.store'), [
            'nome' => 'Sem permissão',
            'icone' => '⛔',
            'descricao' => 'Não deve ser criada.',
            'pontos_bonus' => 15,
        ])->assertForbidden();

        $this->get(route('badges.edit', $badge))->assertForbidden();
        $this->put(route('badges.update', $badge), [])->assertForbidden();
        $this->delete(route('badges.destroy', $badge))->assertForbidden();
    }

    public function test_badge_exige_dados_validos_e_nome_unico(): void
    {
        $professor = User::factory()->create(['tipo' => 'professor']);
        $badge = Badge::firstOrFail();

        $this->actingAs($professor)
            ->from(route('badges.create'))
            ->post(route('badges.store'), [
                'nome' => $badge->nome,
                'icone' => '',
                'descricao' => '',
                'pontos_bonus' => -1,
            ])
            ->assertRedirect(route('badges.create'))
            ->assertSessionHasErrors(['nome', 'icone', 'descricao', 'pontos_bonus']);

        $this->assertSame(1, Badge::where('nome', $badge->nome)->count());
    }
}
