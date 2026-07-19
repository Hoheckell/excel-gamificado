<?php

namespace App\Http\Controllers;

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

        if ($turmas->isNotEmpty()) {
            $turmaId = $request->get('turma_id', $turmas->first()->id);
            $turmaSelecionada = $turmas->firstWhere('id', $turmaId) ?? $turmas->first();

            $equipes = Equipe::where('turma_id', $turmaSelecionada->id)
                ->withCount('alunos')
                ->with(['alunos' => function ($q) {
                    $q->select('id', 'name', 'equipe_id');
                }])
                ->orderBy('pontuacao', 'desc')
                ->get()
                ->map(function ($equipe) {
                    $equipe->categoria_atual = Categoria::paraPontuacao($equipe->pontuacao);
                    $equipe->missoes_com_pontuacao = $equipe->missoes()
                        ->with(['progresso' => function ($q) use ($equipe) {
                            $q->where('equipe_id', $equipe->id)
                              ->with('user:id,name');
                        }])
                        ->get();
                    return $equipe;
                });
        }

        return view('placar.index', compact('turmas', 'turmaSelecionada', 'equipes'));
    }
}
