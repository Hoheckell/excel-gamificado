<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->badges() as $badge) {
            Badge::updateOrCreate(['nome' => $badge['nome']], $badge);
        }
    }

    private function badges(): array
    {
        return [
            ['nome' => 'Zero Mouse', 'icone' => '⌨️', 'descricao' => 'Dominou os atalhos de teclado.', 'pontos_bonus' => 15],
            ['nome' => 'Código Limpo', 'icone' => '🧹', 'descricao' => 'Criou fórmulas claras e sem erros.', 'pontos_bonus' => 15],
            ['nome' => 'Visual Executivo', 'icone' => '🎨', 'descricao' => 'Entregou uma planilha com acabamento profissional.', 'pontos_bonus' => 15],
            ['nome' => 'Salva-Vidas', 'icone' => '🤝', 'descricao' => 'Ajudou outra equipe sem assumir o computador.', 'pontos_bonus' => 15],
        ];
    }
}
