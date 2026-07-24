<?php

namespace Tests\Feature;

use Tests\TestCase;

class RulesPageTest extends TestCase
{
    public function test_pagina_de_apresentacao_e_publica_e_nao_expoe_regras_internas(): void
    {
        $this->get(route('regras'))
            ->assertOk()
            ->assertSee('Aprender Excel de forma prática, colaborativa e motivadora')
            ->assertSee('Aprender fazendo')
            ->assertSee('Colaborar')
            ->assertSee('Acompanhar a evolução')
            ->assertSee('Entrar no sistema')
            ->assertDontSee('Bônus de Colaboração')
            ->assertDontSee('Badge Salva-Vidas')
            ->assertDontSee('500 Pontos');
    }

    public function test_pagina_inicial_apresenta_link_para_conhecer_o_sistema(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Conheça o Sistema')
            ->assertSee(route('regras'), false);
    }
}
