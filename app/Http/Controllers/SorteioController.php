<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SorteioController extends Controller
{
    public function create(): View
    {
        $turmas = auth()->user()->turmas()->orderBy('codigo')->get();

        if ($turmas->isEmpty()) {
            return redirect()->route('equipes.index')
                ->with('error', 'Você não possui turmas cadastradas.');
        }

        return view('sorteio.create', compact('turmas'));
    }

    public function sortear(Request $request): View
    {
        $validated = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'integrantes' => 'required|integer|min:1|max:20',
        ]);

        $professor = $request->user();
        $turmasProfessor = $professor->turmas()->pluck('turmas.id');

        if (! $turmasProfessor->contains($validated['turma_id'])) {
            abort(403, 'Você não gerencia esta turma.');
        }

        $turma = Turma::with('alunos')->findOrFail($validated['turma_id']);
        $integrantes = $validated['integrantes'];

        $alunos = $turma->alunos;

        if ($alunos->isEmpty()) {
            return back()->with('error', 'Esta turma não possui alunos cadastrados.');
        }

        if ($alunos->count() < $integrantes) {
            return back()->with('error', "A turma possui apenas {$alunos->count()} alunos. Mínimo {$integrantes} por equipe.");
        }

        $alunosArray = $alunos->shuffle()->values();
        $equipesSorteadas = $alunosArray->chunk($integrantes);

        // Se a última equipe tiver menos da metade, redistribui
        $totalEquipes = $equipesSorteadas->count();
        $ultima = $equipesSorteadas->last();

        if ($totalEquipes > 1 && $ultima->count() < ceil($integrantes / 2)) {
            $sobrando = $ultima;
            $equipesSorteadas->pop();
            $i = 0;
            foreach ($sobrando as $aluno) {
                $equipesSorteadas[$i % ($totalEquipes - 1)]->push($aluno);
                $i++;
            }
        }

        return view('sorteio.resultado', compact('turma', 'equipesSorteadas', 'integrantes'));
    }

    public function concluir(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'equipes' => 'required|array|min:1',
            'equipes.*.nome' => 'required|string|max:255',
            'equipes.*.alunos' => 'required|array|min:1',
            'equipes.*.alunos.*' => 'exists:users,id',
        ]);

        $professor = $request->user();
        $turmasProfessor = $professor->turmas()->pluck('turmas.id');

        if (! $turmasProfessor->contains($validated['turma_id'])) {
            abort(403);
        }

        foreach ($validated['equipes'] as $equipeData) {
            $equipe = Equipe::create([
                'turma_id' => $validated['turma_id'],
                'nome' => $equipeData['nome'],
                'pontuacao' => 0,
            ]);

            User::whereIn('id', $equipeData['alunos'])
                ->update(['equipe_id' => $equipe->id]);
        }

        return redirect()->route('equipes.index')
            ->with('success', 'Sorteio concluído! ' . count($validated['equipes']) . ' equipes criadas com sucesso.');
    }
}
