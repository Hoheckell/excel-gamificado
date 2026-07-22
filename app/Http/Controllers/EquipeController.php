<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Equipe;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EquipeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Equipe::with([
            'turma:id,codigo,descricao',
            'alunos:id,name,equipe_id',
            'missoes:id,titulo,descricao,pontuacao,permite_resposta,permite_anexo',
            'missoes.progresso',
        ]);

        if ($request->filled('turma_id')) {
            $query->where('turma_id', $request->turma_id);
        }

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%'.$request->search.'%');
        }

        $equipes = $query->orderBy('nome')->paginate(20);
        $turmas = Turma::orderBy('codigo')->get();

        return view('equipes.index', compact('equipes', 'turmas'));
    }

    public function create(Request $request): View
    {
        $turmas = $request->user()->isProfessor()
            ? $request->user()->turmas()->orderBy('codigo')->get()
            : Turma::orderBy('codigo')->get();

        return view('equipes.create', compact('turmas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'nome' => 'required|string|max:255',
            'pontuacao' => 'nullable|integer|min:0',
        ]);

        $professor = $request->user();
        if ($professor->isProfessor()) {
            $turmasProfessor = $professor->turmas()->pluck('turmas.id');
            if (! $turmasProfessor->contains($validated['turma_id'])) {
                abort(403, 'Você não gerencia esta turma.');
            }
        }

        Equipe::create($validated);

        return redirect()->route('equipes.index')
            ->with('success', 'Equipe criada com sucesso.');
    }

    public function show(Equipe $equipe): View
    {
        $equipe->load(['turma:id,codigo,descricao', 'alunos:id,name,equipe_id']);

        return view('equipes.show', compact('equipe'));
    }

    public function edit(Equipe $equipe): View
    {
        $turmas = auth()->user()->isProfessor()
            ? auth()->user()->turmas()->orderBy('codigo')->get()
            : Turma::where('id', $equipe->turma_id)->get();

        return view('equipes.edit', compact('equipe', 'turmas'));
    }

    public function update(Request $request, Equipe $equipe): RedirectResponse
    {
        $validated = $request->validate([
            'turma_id' => 'required|exists:turmas,id',
            'nome' => 'required|string|max:255',
        ]);

        $professor = $request->user();
        if ($professor->isProfessor()) {
            $turmasProfessor = $professor->turmas()->pluck('turmas.id');
            if (! $turmasProfessor->contains($validated['turma_id'])) {
                abort(403, 'Você não gerencia esta turma.');
            }
        }

        $equipe->update($validated);

        return redirect()->route('equipes.index')
            ->with('success', 'Equipe atualizada com sucesso.');
    }

    public function destroy(Equipe $equipe): RedirectResponse
    {
        $equipe->delete();

        return redirect()->route('equipes.index')
            ->with('success', 'Equipe removida com sucesso.');
    }

    // ─── Pontuação ───────────────────────────────────────────────

    public function addPoints(Request $request, Equipe $equipe): RedirectResponse
    {
        $this->authorize('managePoints', $equipe);

        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:1000',
        ]);

        $equipe->increment('pontuacao', $validated['points']);

        return redirect()->route('equipes.index')
            ->with('success', "+{$validated['points']} pontos adicionados à equipe {$equipe->nome}.");
    }

    public function removePoints(Request $request, Equipe $equipe): RedirectResponse
    {
        $this->authorize('managePoints', $equipe);

        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:1000',
        ]);

        $equipe->update(['pontuacao' => max(0, $equipe->pontuacao - $validated['points'])]);

        return redirect()->route('equipes.index')
            ->with('success', "-{$validated['points']} pontos removidos da equipe {$equipe->nome}.");
    }

    public function concederBadge(Request $request, Equipe $equipe): RedirectResponse
    {
        $this->authorize('managePoints', $equipe);

        $validated = $request->validate(['badge_id' => 'required|exists:badges,id']);
        $badge = Badge::findOrFail($validated['badge_id']);
        $equipe->badges()->syncWithoutDetaching([$badge->id]);

        return back()->with('success', "Badge {$badge->nome} concedida à equipe {$equipe->nome}.");
    }

    public function removerBadge(Equipe $equipe, Badge $badge): RedirectResponse
    {
        $this->authorize('managePoints', $equipe);
        $equipe->badges()->detach($badge->id);

        return back()->with('success', "Badge {$badge->nome} removida da equipe {$equipe->nome}.");
    }

    // ─── Alunos ──────────────────────────────────────────────────

    public function addAluno(Request $request, Equipe $equipe): RedirectResponse
    {
        $this->authorize('manageAlunos', $equipe);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $aluno = User::where('id', $validated['user_id'])
            ->where('tipo', 'aluno')
            ->firstOrFail();

        $aluno->turmas()->syncWithoutDetaching([$equipe->turma_id]);
        $aluno->update(['equipe_id' => $equipe->id]);

        return redirect()->route('equipes.index')
            ->with('success', "{$aluno->name} adicionado à equipe {$equipe->nome}.");
    }

    public function removeAluno(Request $request, Equipe $equipe): RedirectResponse
    {
        $this->authorize('manageAlunos', $equipe);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        User::where('id', $validated['user_id'])
            ->where('equipe_id', $equipe->id)
            ->update(['equipe_id' => null]);

        return redirect()->route('equipes.index')
            ->with('success', "Aluno removido da equipe {$equipe->nome}.");
    }

    public function criarPorAluno(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome_equipe' => 'required|string|max:255',
            'aluno_ids' => 'required|array|min:1',
            'aluno_ids.*' => 'exists:users,id',
            'senha_professor' => 'required|string',
        ]);

        $aluno = $request->user();
        if (! $aluno->isAluno()) {
            abort(403);
        }

        $turma = $aluno->turmas()->first();
        if (! $turma) {
            return back()->with('error', 'Você não está vinculado a uma turma.');
        }

        $professor = $turma->professores()->first();
        if (! $professor || ! Hash::check($validated['senha_professor'], $professor->password)) {
            return back()->with('error', 'Senha do professor incorreta.');
        }

        $alunoIds = $validated['aluno_ids'];
        $alunoIds[] = $aluno->id;
        $alunoIds = array_unique($alunoIds);

        // Verifica se todos os alunos são da turma e não têm equipe
        $alunosTurma = $turma->alunos()->pluck('users.id')->toArray();
        foreach ($alunoIds as $id) {
            if (! in_array($id, $alunosTurma)) {
                return back()->with('error', 'Todos os alunos devem ser da mesma turma.');
            }
        }

        $alunosComEquipe = User::whereIn('id', $alunoIds)
            ->whereNotNull('equipe_id')
            ->pluck('name')
            ->toArray();

        if (! empty($alunosComEquipe)) {
            return back()->with('error', 'Os seguintes alunos já estão em uma equipe: '.implode(', ', $alunosComEquipe));
        }

        $equipe = Equipe::create([
            'turma_id' => $turma->id,
            'nome' => $validated['nome_equipe'],
            'pontuacao' => 0,
        ]);

        User::whereIn('id', $alunoIds)->update(['equipe_id' => $equipe->id]);

        return redirect()->route('equipes.index')
            ->with('success', "Equipe {$equipe->nome} criada com ".count($alunoIds).' alunos.');
    }

    public function sairDaEquipe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'senha_professor' => 'required|string',
        ]);

        $aluno = $request->user();
        if (! $aluno->isAluno() || $aluno->equipe_id != $validated['equipe_id']) {
            abort(403);
        }

        $turma = $aluno->turmas()->first();
        $professor = $turma?->professores()->first();

        if (! $professor || ! Hash::check($validated['senha_professor'], $professor->password)) {
            return back()->with('error', 'Senha do professor incorreta.');
        }

        $aluno->update(['equipe_id' => null]);

        return redirect()->route('equipes.index')
            ->with('success', 'Você saiu da equipe.');
    }
}
