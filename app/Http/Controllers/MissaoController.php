<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\EquipeMissaoUser;
use App\Models\Missao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MissaoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Missao::class, 'missao');
    }

    public function index(): View
    {
        $missoes = Missao::with('equipes:id,nome')
            ->withCount('equipes')
            ->orderBy('ordem')
            ->paginate(15);

        return view('missoes.index', compact('missoes'));
    }

    public function create(): View
    {
        return view('missoes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'ordem' => 'required|integer|min:1',
            'descricao' => 'required|string|max:5000',
            'pontuacao' => 'required|integer|min:1|max:500',
        ]);

        Missao::create($validated);

        return redirect()->route('missoes.index')
            ->with('success', 'Missão criada com sucesso.');
    }

    public function show(Missao $missao): View
    {
        $missao->load('equipes:id,nome,turma_id');
        return view('missoes.show', compact('missao'));
    }

    public function edit(Missao $missao): View
    {
        return view('missoes.edit', compact('missao'));
    }

    public function update(Request $request, Missao $missao): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'ordem' => 'required|integer|min:1',
            'descricao' => 'required|string|max:5000',
            'pontuacao' => 'required|integer|min:1|max:500',
        ]);

        $missao->update($validated);

        return redirect()->route('missoes.index')
            ->with('success', 'Missão atualizada.');
    }

    public function destroy(Missao $missao): RedirectResponse
    {
        $missao->delete();

        return redirect()->route('missoes.index')
            ->with('success', 'Missão removida.');
    }

    public function atribuir(Request $request, Missao $missao): RedirectResponse
    {
        $this->authorize('atribuirEquipes', $missao);

        $validated = $request->validate([
            'equipe_ids' => 'required|array|min:1',
            'equipe_ids.*' => 'exists:equipes,id',
        ]);

        $missao->equipes()->syncWithoutDetaching($validated['equipe_ids']);

        return back()->with('success', 'Equipes vinculadas à missão.');
    }

    public function removerEquipe(Request $request, Missao $missao): RedirectResponse
    {
        $this->authorize('atribuirEquipes', $missao);

        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
        ]);

        $missao->equipes()->detach($validated['equipe_id']);

        return back()->with('success', 'Equipe removida da missão.');
    }

    // ─── Controle de Missão pelo Aluno ─────────────────────────────

    public function iniciar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
            'papeis' => 'required|array',
            'papeis.*' => ['required', Rule::in(['arquiteto', 'auditor', 'designer', 'gestor'])],
        ]);

        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validated['equipe_id']) {
            abort(403);
        }

        $equipe = Equipe::with('alunos:id,equipe_id')->findOrFail($validated['equipe_id']);
        $membros = $equipe->alunos->pluck('id')->map(fn ($id) => (string) $id)->sort()->values();
        $informados = collect(array_keys($validated['papeis']))->map(fn ($id) => (string) $id)->sort()->values();

        if ($membros->values()->all() !== $informados->values()->all()) {
            return back()->withErrors(['papeis' => 'Defina um papel para todos os membros ativos da equipe.']);
        }

        $missao = Missao::findOrFail($validated['missao_id']);
        $missaoAnterior = Missao::where('ordem', $missao->ordem - 1)->first();

        if ($missaoAnterior) {
            $papeisAnteriores = EquipeMissaoUser::where('equipe_id', $equipe->id)
                ->where('missao_id', $missaoAnterior->id)
                ->pluck('papel', 'user_id');

            foreach ($validated['papeis'] as $userId => $papel) {
                if ($papeisAnteriores->get((int) $userId) === $papel) {
                    return back()->withErrors([
                        'papeis' => 'O rodízio exige que cada integrante use um papel diferente da missão anterior.',
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validated, $equipe): void {
            foreach ($validated['papeis'] as $userId => $papel) {
                EquipeMissaoUser::updateOrCreate(
                    [
                        'equipe_id' => $equipe->id,
                        'missao_id' => $validated['missao_id'],
                        'user_id' => $userId,
                    ],
                    [
                        'papel' => $papel,
                        'status' => 'em_andamento',
                        'started_at' => now(),
                        'finished_at' => null,
                    ]
                );
            }
        });

        return back()->with('success', 'Missão iniciada!');
    }

    public function finalizar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
        ]);

        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validated['equipe_id']) {
            abort(403);
        }

        $registro = EquipeMissaoUser::where([
            'equipe_id' => $validated['equipe_id'],
            'missao_id' => $validated['missao_id'],
            'user_id' => $user->id,
        ])->first();

        if (! $registro || $registro->status !== 'em_andamento') {
            return back()->with('error', 'Missão não está em andamento.');
        }

        $registro->update([
            'status' => 'concluida',
            'finished_at' => now(),
        ]);

        return back()->with('success', 'Missão concluída em ' . $registro->duracao . '!');
    }

    public function pontuar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'registro_id' => 'required|exists:equipe_missao_user,id',
            'pontuacao' => 'required|integer|min:0',
        ]);

        $registro = EquipeMissaoUser::findOrFail($validated['registro_id']);
        $this->authorize('atribuirEquipes', $registro->missao);
        if ($validated['pontuacao'] > $registro->missao->pontuacao) {
            return back()->withErrors(['pontuacao' => 'A pontuação não pode superar o valor da missão.']);
        }
        $registro->update(['pontuacao_obtida' => $validated['pontuacao']]);

        return back()->with('success', 'Pontuação atribuída.');
    }
}
