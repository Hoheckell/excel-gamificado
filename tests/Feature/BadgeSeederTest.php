<?php

namespace Tests\Feature;

use App\Models\Badge;
use Database\Seeders\BadgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_e_idempotente_e_preserva_badges_existentes(): void
    {
        $personalizada = Badge::create([
            'nome' => 'Badge personalizada',
            'icone' => '⭐',
            'descricao' => 'Criada pelo professor.',
            'pontos_bonus' => 30,
        ]);

        $this->seed(BadgeSeeder::class);
        $this->seed(BadgeSeeder::class);

        $this->assertDatabaseHas('badges', [
            'id' => $personalizada->id,
            'nome' => 'Badge personalizada',
            'pontos_bonus' => 30,
        ]);
        $this->assertSame(1, Badge::where('nome', 'Zero Mouse')->count());
        $this->assertSame(1, Badge::where('nome', 'Código Limpo')->count());
        $this->assertSame(1, Badge::where('nome', 'Visual Executivo')->count());
        $this->assertSame(1, Badge::where('nome', 'Salva-Vidas')->count());
    }
}
