<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\EquipeMissao;
use App\Models\EquipeMissaoUser;
use App\Models\Missao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MissaoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Missao::class, 'missao');
    }

    public function index(): View
    {
        $missoes = Missao::with(['equipes:id,nome', 'equipes.alunos:id,name,equipe_id', 'progresso'])
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
            'permite_resposta' => 'sometimes|boolean',
            'permite_anexo' => 'sometimes|boolean',
        ]);

        $validated['permite_resposta'] = $request->boolean('permite_resposta');
        $validated['permite_anexo'] = $request->boolean('permite_anexo');

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
            'permite_resposta' => 'sometimes|boolean',
            'permite_anexo' => 'sometimes|boolean',
        ]);

        $validated['permite_resposta'] = $request->boolean('permite_resposta');
        $validated['permite_anexo'] = $request->boolean('permite_anexo');

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
        $ausentes = EquipeMissaoUser::where('equipe_id', $equipe->id)
            ->where('missao_id', $validated['missao_id'])
            ->where('status', 'ausente')
            ->pluck('user_id');
        $membros = $equipe->alunos->whereNotIn('id', $ausentes)
            ->pluck('id')->map(fn ($id) => (string) $id)->sort()->values();
        $informados = collect(array_keys($validated['papeis']))->map(fn ($id) => (string) $id)->sort()->values();

        if ($membros->isEmpty()) {
            return back()->withErrors(['papeis' => 'A missão precisa ter pelo menos um membro presente.']);
        }

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

    public function comunicarFalta(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validated['equipe_id']) {
            abort(403);
        }
        if ($user->id == $validated['user_id']) {
            return back()->withErrors(['falta' => 'Você não pode comunicar a própria falta.']);
        }

        $equipe = Equipe::with('alunos:id,equipe_id')->findOrFail($validated['equipe_id']);
        if (! $equipe->alunos->contains('id', $validated['user_id'])) {
            abort(403);
        }
        if (! $equipe->missoes()->whereKey($validated['missao_id'])->exists()) {
            abort(403);
        }

        $ausentesAtuais = EquipeMissaoUser::where('equipe_id', $equipe->id)
            ->where('missao_id', $validated['missao_id'])
            ->where('status', 'ausente')
            ->pluck('user_id');
        if ($equipe->alunos->pluck('id')->diff($ausentesAtuais)->count() <= 1) {
            return back()->withErrors(['falta' => 'A missão precisa manter pelo menos um integrante presente.']);
        }

        $registro = EquipeMissaoUser::where([
            'equipe_id' => $validated['equipe_id'],
            'missao_id' => $validated['missao_id'],
            'user_id' => $validated['user_id'],
        ])->first();
        if ($registro?->status === 'ausente') {
            return back()->withErrors(['falta' => 'A falta deste integrante já foi comunicada e não pode ser desfeita.']);
        }
        if ($registro?->status === 'concluida') {
            return back()->withErrors(['falta' => 'Não é possível comunicar falta para quem já concluiu a missão.']);
        }

        EquipeMissaoUser::updateOrCreate(
            [
                'equipe_id' => $validated['equipe_id'],
                'missao_id' => $validated['missao_id'],
                'user_id' => $validated['user_id'],
            ],
            [
                'status' => 'ausente',
                'started_at' => null,
                'finished_at' => null,
                'pontuacao_obtida' => null,
            ]
        );

        return back()->with('success', 'Falta comunicada. O integrante não bloqueará a conclusão desta missão.');
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

        return back()->with('success', 'Missão concluída em '.$registro->duracao.'!');
    }

    public function entregar(Request $request): RedirectResponse
    {
        $validatedIds = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
        ]);
        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validatedIds['equipe_id']) {
            abort(403);
        }

        $missao = Missao::findOrFail($validatedIds['missao_id']);
        $validated = $request->validate([
            'resposta' => [Rule::prohibitedIf(! $missao->permite_resposta), 'nullable', 'string', 'max:5000'],
            'anexo' => [Rule::prohibitedIf(! $missao->permite_anexo), 'nullable', 'file', 'max:10240'],
        ]);
        $equipe = Equipe::with('alunos:id,equipe_id')->findOrFail($validatedIds['equipe_id']);
        $ausentes = EquipeMissaoUser::where($validatedIds)
            ->where('status', 'ausente')
            ->pluck('user_id');
        $membrosAtivos = $equipe->alunos->whereNotIn('id', $ausentes)->pluck('id');
        $concluidos = EquipeMissaoUser::where($validatedIds)
            ->whereIn('user_id', $membrosAtivos)
            ->where('status', 'concluida')
            ->count();
        if ($membrosAtivos->isEmpty() || $concluidos !== $membrosAtivos->count()) {
            return back()->withErrors(['entrega' => 'A entrega só é liberada após todos os membros concluírem a missão.']);
        }

        $entrega = EquipeMissao::where($validatedIds)->firstOrFail();
        $dados = ['resposta' => $validated['resposta'] ?? null];
        if ($request->hasFile('anexo')) {
            $dados['anexo_path'] = $request->file('anexo')->store('anexos-missoes');
            $dados['anexo_nome_original'] = $request->file('anexo')->getClientOriginalName();
        }
        $entrega->update($dados);

        return back()->with('success', 'Entrega da equipe enviada com sucesso.');
    }

    public function baixarAnexo(Request $request, EquipeMissao $entrega): StreamedResponse
    {
        if ($request->user()->equipe_id !== $entrega->equipe_id) {
            $this->authorize('atribuirEquipes', $entrega->missao);
        }

        abort_unless($entrega->anexo_path && Storage::exists($entrega->anexo_path), 404);

        return Storage::download($entrega->anexo_path, $entrega->anexo_nome_original);
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
