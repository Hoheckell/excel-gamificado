<?php

namespace App\Http\Controllers;

use App\Models\Turma;
use App\Services\ConcluirTurma;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TurmaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Turma::class, 'turma');
    }

    public function index(Request $request): View
    {
        $query = Turma::query()
            ->withCount(['alunos', 'equipes', 'professores'])
            ->with(['users' => function ($q) {
                $q->where('users.tipo', 'professor')->select('users.id', 'users.name');
            }]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('codigo', 'like', '%'.$request->search.'%')
                    ->orWhere('descricao', 'like', '%'.$request->search.'%');
            });
        }

        $turmas = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('turmas.index', compact('turmas'));
    }

    public function create(): View
    {
        return view('turmas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'dt_inicio' => 'required|date',
            'dt_fim' => 'required|date|after_or_equal:dt_inicio',
        ]);

        $turma = Turma::create([
            'codigo' => $this->gerarCodigoUnico(),
            'descricao' => $validated['descricao'],
            'dt_inicio' => $validated['dt_inicio'],
            'dt_fim' => $validated['dt_fim'],
        ]);

        $turma->users()->attach($request->user()->id);

        return redirect()->route('turmas.index')
            ->with('success', "Turma {$turma->codigo} criada com sucesso.");
    }

    public function show(Turma $turma): View
    {
        $turma->loadCount(['alunos', 'equipes', 'professores']);
        $turma->load(['equipes:id,nome,turma_id', 'users:id,name,tipo']);

        return view('turmas.show', compact('turma'));
    }

    public function edit(Turma $turma): View
    {
        return view('turmas.edit', compact('turma'));
    }

    public function update(Request $request, Turma $turma): RedirectResponse
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'dt_inicio' => 'required|date',
            'dt_fim' => 'required|date|after_or_equal:dt_inicio',
        ]);

        $turma->update($validated);

        return redirect()->route('turmas.index')
            ->with('success', "Turma {$turma->codigo} atualizada.");
    }

    public function destroy(Turma $turma): RedirectResponse
    {
        $codigo = $turma->codigo;
        $turma->delete();

        return redirect()->route('turmas.index')
            ->with('success', "Turma {$codigo} removida.");
    }

    public function concluir(Turma $turma, ConcluirTurma $concluirTurma): RedirectResponse
    {
        $this->authorize('concluir', $turma);

        if ($turma->concluida_em) {
            return back()->with('success', "A turma {$turma->codigo} já estava concluída.");
        }

        $concluirTurma->execute($turma);

        return redirect()->route('turmas.show', $turma)
            ->with('success', "Turma {$turma->codigo} concluída. Todos os anexos foram apagados.");
    }

    private function gerarCodigoUnico(): string
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
        } while (Turma::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
