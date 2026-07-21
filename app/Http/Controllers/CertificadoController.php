<?php

namespace App\Http\Controllers;

use App\Mail\CertificadoEnviado;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Turma;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class CertificadoController extends Controller
{
    public function emitir(Request $request): View
    {
        $user = $request->user();

        if (! $user->isProfessor() && ! $user->autorizado) {
            abort(403, 'Você não está autorizado a emitir certificados. Solicite a autorização ao seu professor.');
        }

        if ($user->isProfessor()) {
            $turma = $user->turmas()
                ->has('alunos')
                ->has('equipes')
                ->with(['alunos', 'equipes'])
                ->first();

            $equipe = $turma?->equipes->first();
            $professor = $user;
        } else {
            $turma = $user->turmas()
                ->has('equipes')
                ->with('equipes')
                ->first();

            if (! $turma) {
                return view('certificados.emitir', [
                    'user' => $user, 'turma' => null, 'equipe' => null,
                    'categoria' => null, 'professor' => null,
                ]);
            }

            $equipe = $user->equipe;
            $professor = $turma->professores()->first();
        }

        $categoria = $equipe ? Categoria::paraPontuacao($equipe->xp_total) : null;

        return view('certificados.emitir', compact('user', 'turma', 'equipe', 'categoria', 'professor'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isProfessor() && ! $user->autorizado) {
            abort(403);
        }

        $validated = $request->validate([
            'nome_aluno' => 'required|string|max:255',
            'cpf_aluno' => 'required|string|max:14',
            'cpf_professor' => 'required|string|max:14',
            'dt_ultima_aula' => 'required|date',
            'confirmo' => 'required|accepted',
        ]);

        $turma = $user->turmas()->first();
        if (! $turma) {
            return back()->with('error', 'Você não está vinculado a nenhuma turma.');
        }

        $equipe = $user->equipe;
        $categoria = $equipe ? Categoria::paraPontuacao($equipe->xp_total) : null;
        $professor = $turma->professores()->first();

        $certificado = Certificado::create([
            'user_id' => $user->id,
            'turma_id' => $turma->id,
            'equipe_id' => $equipe?->id,
            'categoria_id' => $categoria?->id,
            'nome_aluno' => $validated['nome_aluno'],
            'cpf_aluno' => $validated['cpf_aluno'],
            'nome_equipe' => $equipe?->nome,
            'nome_categoria' => $categoria?->nome,
            'titulo_certificado' => $categoria?->titulo_certificado,
            'cpf_professor' => $validated['cpf_professor'],
            'nome_professor' => $professor?->name,
            'dt_inicio' => $turma->dt_inicio,
            'dt_fim' => $turma->dt_fim,
            'dt_ultima_aula' => $validated['dt_ultima_aula'],
            'codigo_validacao' => Str::upper(Str::random(16)),
            'emitido_em' => now(),
        ]);

        try {
            Mail::to($user->email)->send(new CertificadoEnviado($certificado));
        } catch (\Exception $e) {
            Log::error('Falha ao enviar email do certificado: ' . $e->getMessage());
        }

        return redirect()->route('certificados.confirmacao', $certificado)
            ->with('success', 'Certificado emitido com sucesso!');
    }

    public function confirmacao(Certificado $certificado): View
    {
        $certificado->load(['user', 'turma', 'equipe', 'categoria']);

        return view('certificados.show', compact('certificado'));
    }

    public function validar(string $codigo): View
    {
        $certificado = Certificado::where('codigo_validacao', $codigo)
            ->with(['user', 'turma', 'equipe', 'categoria'])
            ->firstOrFail();

        return view('certificados.validar', compact('certificado'));
    }
}
