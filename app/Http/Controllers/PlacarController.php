<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Categoria;
use App\Models\Equipe;
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
            'icone' => '🌱',
            'texto' => 'A turma está construindo as bases',
        ];

        if ($turmas->isNotEmpty()) {
            $turmaId = $request->get('turma_id', $turmas->first()->id);
            $turmaSelecionada = $turmas->firstWhere('id', $turmaId) ?? $turmas->first();

            // 1. Consulta Principal das Equipes (Atualizada com Badges e Papéis GBL)
            $equipes = Equipe::where('turma_id', $turmaSelecionada->id)
                ->when($user->isAluno(), fn ($query) => $query->whereKey($user->equipe_id))
                ->withCount('alunos')
                ->with([
                    'alunos' => function ($q) {
                        $q->select('id', 'name', 'equipe_id');
                    },
                    'badges', // <-- ADICIONADO: Eager loading para alimentar a vitrine de medalhas sem N+1 queries
                ])
                ->get()
                ->map(function ($equipe) use ($user) {
                    $equipe->categoria_atual = Categoria::paraPontuacao($equipe->xp_total);

                    // Mantemos sua lógica, mas garantindo que o progresso traga a coluna 'papel'
                    $equipe->missoes_com_pontuacao = $equipe->missoes()
                        ->with(['progresso' => function ($q) use ($equipe, $user) {
                            $q->where('equipe_id', $equipe->id)
                                ->when($user->isAluno(), fn ($query) => $query->where('user_id', $user->id))
                                ->with(['user:id,name', 'papeis']);
                            // Como o select padrão é '*', a nova coluna 'papel' já virá inclusa aqui!
                        }])
                        ->get();

                    return $equipe;
                })
                ->when($user->isProfessor(), fn ($equipes) => $equipes->sortByDesc('xp_total'))
                ->values();

            // 2. Cálculo do "Zequinhômetro" (Humor do Chefe para o Header)
            $mediaTurma = $equipes->avg('xp_total') ?? 0;

            $humorChefe = match (true) {
                $mediaTurma > 350 => [
                    'estado' => 'satisfeito',
                    'icone' => '🌟',
                    'texto' => 'A turma consolidou aprendizagens importantes',
                ],
                $mediaTurma >= 200 => [
                    'estado' => 'tenso',
                    'icone' => '🧭',
                    'texto' => 'A turma está avançando com consistência',
                ],
                default => [
                    'estado' => 'panico',
                    'icone' => '🌱',
                    'texto' => 'A turma está construindo as bases',
                ],
            };

            // 3. Retorno para a View (Não esqueça de passar a variável $humorChefe)
            return view('placar.index', compact('equipes', 'turmas', 'turmaSelecionada', 'humorChefe', 'badges'));
        }

        // Caso não haja turmas, retorna view vazia
        return view('placar.index', compact('equipes', 'turmas', 'turmaSelecionada', 'humorChefe', 'badges'));
    }
}
