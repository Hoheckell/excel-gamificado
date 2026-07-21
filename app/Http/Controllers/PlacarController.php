<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Categoria;
use App\Models\Equipe;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlacarController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isProfessor()) {
            $turmas = $user->turmas()->orderBy('codigo')->get();
        } else {
            $turmas = $user->turmas()->whereDate('dt_fim', '>=', now())->orderBy('codigo')->get();
        }

        $turmaSelecionada = null;
        $equipes = collect();
        $badges = Badge::orderBy('nome')->get();
        $humorChefe = [
            'estado' => 'panico',
            'icone' => '😱',
            'texto' => 'Zequinha em Pânico / Chefe Zangado',
        ];

        if ($turmas->isNotEmpty()) {
            $turmaId = $request->get('turma_id', $turmas->first()->id);
            $turmaSelecionada = $turmas->firstWhere('id', $turmaId) ?? $turmas->first();

                // 1. Consulta Principal das Equipes (Atualizada com Badges e Papéis GBL)
                $equipes = Equipe::where('turma_id', $turmaSelecionada->id)
                    ->withCount('alunos')
                    ->with([
                        'alunos' => function ($q) {
                            $q->select('id', 'name', 'equipe_id');
                        },
                        'badges' // <-- ADICIONADO: Eager loading para alimentar a vitrine de medalhas sem N+1 queries
                    ])
                    ->get()
                    ->map(function ($equipe) {
                        $equipe->categoria_atual = Categoria::paraPontuacao($equipe->xp_total);
                        
                        // Mantemos sua lógica, mas garantindo que o progresso traga a coluna 'papel'
                        $equipe->missoes_com_pontuacao = $equipe->missoes()
                            ->with(['progresso' => function ($q) use ($equipe) {
                                $q->where('equipe_id', $equipe->id)
                                ->with('user:id,name'); 
                                // Como o select padrão é '*', a nova coluna 'papel' já virá inclusa aqui!
                            }])
                            ->get();
                            
                        return $equipe;
                    })
                    ->sortByDesc('xp_total')
                    ->values();

                // 2. Cálculo do "Zequinhômetro" (Humor do Chefe para o Header)
                $mediaTurma = $equipes->avg('xp_total') ?? 0;

                $humorChefe = match (true) {
                    $mediaTurma > 350 => [
                        'estado' => 'satisfeito',
                        'icone' => '😎',
                        'texto' => 'Juvenildo Satisfeito / Zequinha Promovido'
                    ],
                    $mediaTurma >= 200 => [
                        'estado' => 'tenso',
                        'icone' => '😰',
                        'texto' => 'Zequinha Sobrevivendo aos Prazos'
                    ],
                    default => [
                        'estado' => 'panico',
                        'icone' => '😱',
                        'texto' => 'Zequinha em Pânico / Chefe Zangado'
                    ],
                };

                // 3. Retorno para a View (Não esqueça de passar a variável $humorChefe)
                return view('placar.index', compact('equipes', 'turmas', 'turmaSelecionada', 'humorChefe', 'badges'));
        }
        
        // Caso não haja turmas, retorna view vazia
        return view('placar.index', compact('equipes', 'turmas', 'turmaSelecionada', 'humorChefe', 'badges'));
    }
}
