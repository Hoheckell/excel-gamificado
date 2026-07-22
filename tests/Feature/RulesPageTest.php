<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RulesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_regras_explicam_bonus_de_colaboracao_e_badge_salva_vidas(): void
    {
        $professor = User::factory()->create(['tipo' => 'professor']);

        $this->actingAs($professor)
            ->get(route('regras'))
            ->assertOk()
            ->assertSee('Regra de ouro: ensinar sem executar pelo colega')
            ->assertSee('Bônus de Colaboração (+20)')
            ->assertSee('Badge Salva-Vidas (+15 XP)')
            ->assertSee('A equipe que ajudou e a equipe ajudada')
            ->assertSee('Somente a equipe que prestou a orientação')
            ->assertSee('100 + 20 + 15 = 135 pontos');
    }
}
