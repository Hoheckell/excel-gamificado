<?php

namespace App\Http\Controllers;

use App\Mail\CertificadoEnviado;
use App\Models\Equipe;
use App\Models\Turma;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AlunoController extends Controller
{
    /**
     * Listagem de alunos. Aluno vê nomes/equipes da sua turma.
     * Professor vê todos os alunos das turmas que gerencia.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isProfessor()) {
            $turmasQuery = $user->turmas();

            // Filtra apenas turmas ativas por padrão; professor pode ver inativas
            if (! $request->boolean('inativas')) {
                $turmasQuery->whereDate('dt_fim', '>=', now());
            }

            $turmasIds = $turmasQuery->pluck('turmas.id');

            $query = User::where('tipo', 'aluno')
                ->whereHas('turmas', function ($q) use ($turmasIds) {
                    $q->whereIn('turmas.id', $turmasIds);
                })
                ->with(['equipe:id,nome', 'turmas:id,codigo', 'certificados']);

            if ($request->filled('turma_id')) {
                $query->whereHas('turmas', function ($q) use ($request) {
                    $q->where('turmas.id', $request->turma_id);
                });
            }

            $alunos = $query->orderBy('name')->paginate(20);

            $turmas = Turma::whereIn('id', $turmasIds)->orderBy('codigo')->get();
            $equipes = Equipe::whereIn('turma_id', $turmasIds)->orderBy('nome')->get();

            $mostrarInativas = $request->boolean('inativas');

            return view('alunos.index', compact('alunos', 'turmas', 'equipes', 'mostrarInativas'));
        }

        // Aluno: vê colegas apenas de turmas ativas
        $turmasIds = $user->turmas()
            ->whereDate('dt_fim', '>=', now())
            ->pluck('turmas.id');

        $alunos = User::where('tipo', 'aluno')
            ->whereHas('turmas', function ($q) use ($turmasIds) {
                $q->whereIn('turmas.id', $turmasIds);
            })
            ->with(['equipe:id,nome'])
            ->select('id', 'name', 'equipe_id')
            ->orderBy('name')
            ->get();

        return view('alunos.index', compact('alunos'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $turmasIds = $user->turmas()->pluck('turmas.id');
        $turmas = Turma::whereIn('id', $turmasIds)->orderBy('codigo')->get();
        $equipes = Equipe::whereIn('turma_id', $turmasIds)->orderBy('nome')->get();

        return view('alunos.create', compact('turmas', 'equipes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'turma_id' => 'required|exists:turmas,id',
            'equipe_id' => 'nullable|exists:equipes,id',
        ]);

        $professor = $request->user();
        $turmasProfessor = $professor->turmas()->pluck('turmas.id');

        if (! $turmasProfessor->contains($validated['turma_id'])) {
            abort(403, 'Você não gerencia esta turma.');
        }

        $aluno = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'tipo' => 'aluno',
            'equipe_id' => $validated['equipe_id'] ?? null,
            'autorizado' => true,
        ]);

        $aluno->turmas()->attach($validated['turma_id']);
        event(new Registered($aluno));

        return redirect()->route('alunos.index')
            ->with('success', 'Aluno cadastrado. Um link de verificação foi enviado ao e-mail informado.');
    }

    public function show(User $aluno): View
    {
        $aluno->load(['equipe:id,nome', 'turmas:id,codigo']);

        return view('alunos.show', compact('aluno'));
    }

    public function edit(User $aluno): View
    {
        $aluno->load(['equipe:id,nome,turma_id', 'turmas:id']);
        /** @var User|null $user */
        $user = Auth::user();
        if ($user->isProfessor()) {
            $turmasIds = $user->turmas()->pluck('turmas.id');
            $turmas = Turma::whereIn('id', $turmasIds)->orderBy('codigo')->get();
            $equipes = Equipe::whereIn('turma_id', $turmasIds)->orderBy('nome')->get();
        } else {
            $turmasIds = $aluno->turmas()->pluck('turmas.id');
            $turmas = Turma::whereIn('id', $turmasIds)->get();
            $equipes = Equipe::whereIn('turma_id', $turmasIds)->orderBy('nome')->get();
        }

        return view('alunos.edit', compact('aluno', 'turmas', 'equipes'));
    }

    public function update(Request $request, User $aluno): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];

        if ($request->user()->isProfessor()) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,'.$aluno->id;
            $rules['turma_id'] = 'required|exists:turmas,id';
            $rules['equipe_id'] = 'nullable|exists:equipes,id';
        }

        $validated = $request->validate($rules);

        $aluno->update(['name' => $validated['name']]);

        if ($request->user()->isProfessor()) {
            $aluno->update([
                'email' => $validated['email'],
                'equipe_id' => $validated['equipe_id'] ?? null,
                'autorizado' => $request->boolean('autorizado'),
            ]);

            $aluno->turmas()->sync([$validated['turma_id']]);
        }

        return redirect()->route('alunos.index')
            ->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(User $aluno): RedirectResponse
    {
        $aluno->delete();

        return redirect()->route('alunos.index')
            ->with('success', 'Aluno removido com sucesso.');
    }

    public function autorizar(User $aluno): RedirectResponse
    {
        $this->authorize('update', $aluno);

        if (! auth()->user()->isProfessor()) {
            abort(403);
        }

        $aluno->update(['autorizado' => ! $aluno->autorizado]);

        $status = $aluno->autorizado ? 'autorizado' : 'desautorizado';

        return back()->with('success', "Aluno {$aluno->name} {$status} para emissão de certificado.");
    }

    public function entrarTurma(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'required|string|size:6',
        ]);

        $turma = Turma::where('codigo', strtoupper($validated['codigo']))
            ->whereDate('dt_fim', '>=', now())
            ->first();

        if (! $turma) {
            return back()->with('error', 'Turma não encontrada ou já encerrada. Verifique o código.');
        }

        $user = $request->user();
        if ($user->turmas()->where('turmas.id', $turma->id)->exists()) {
            return back()->with('error', 'Você já está nesta turma.');
        }

        $user->turmas()->attach($turma->id);

        return redirect()->route('dashboard')
            ->with('success', "Você entrou na turma {$turma->codigo} — {$turma->descricao}!");
    }

    public function reenviarCertificado(User $aluno): RedirectResponse
    {
        $user = auth()->user();
        if (! $user->isProfessor()) {
            abort(403);
        }

        $certificado = $aluno->certificados()->latest()->first();
        if (! $certificado) {
            return back()->with('error', 'Este aluno não possui certificado emitido.');
        }

        try {
            Mail::to($aluno->email)->send(new CertificadoEnviado($certificado));
        } catch (\Exception $e) {
            \Log::error('Falha ao reenviar certificado: '.$e->getMessage());

            return back()->with('error', 'Falha ao enviar o e-mail. Tente novamente.');
        }

        return back()->with('success', "Certificado reenviado para {$aluno->email}.");
    }
}
