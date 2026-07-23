<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\EquipeMissaoUser;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isProfessor() || ! $user->equipe_id) {
            return view('dashboard', ['jornada' => null]);
        }

        $equipe = $user->equipe()->with('badges')->firstOrFail();
        $missoes = $equipe->missoes()
            ->with(['progresso' => fn ($query) => $query->where('user_id', $user->id)])
            ->orderBy('ordem')
            ->get();

        $progressoPorMissao = $missoes->mapWithKeys(
            fn ($missao) => [$missao->id => $missao->progresso->first()]
        );
        $missaoAtual = $missoes->first(function ($missao) use ($progressoPorMissao) {
            $progresso = $progressoPorMissao->get($missao->id);

            return $progresso?->status === 'em_andamento' || ! $progresso || $progresso->status === 'pendente';
        });
        $progressoAtual = $missaoAtual ? $progressoPorMissao->get($missaoAtual->id) : null;
        $ultimoFeedback = EquipeMissaoUser::query()
            ->where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNotNull('feedback_professor')
                    ->orWhereNotNull('pontuacao_obtida');
            })
            ->with('missao')
            ->latest('updated_at')
            ->first();
        $concluidas = $progressoPorMissao->filter(fn ($progresso) => $progresso?->status === 'concluida')->count();
        $entregaAtual = $missaoAtual?->pivot;

        $proximaAcao = match (true) {
            $missoes->isEmpty() => 'Aguarde a primeira missão ser atribuída à sua equipe.',
            ! $missaoAtual => 'Jornada concluída. Revise seus feedbacks e reconheça o que você aprendeu.',
            ! $progressoAtual => 'Combine os papéis com sua equipe e inicie a missão.',
            $progressoAtual->status === 'em_andamento' => 'Pratique sua função e finalize sua participação quando terminar.',
            $progressoAtual->status === 'concluida' && ! ($entregaAtual?->resposta || $entregaAtual?->anexo_path) => 'Aguarde todos concluírem e envie o Arquivo Mestre da equipe.',
            $progressoAtual->status === 'concluida' && $progressoAtual->pontuacao_obtida === null => 'Entrega realizada. Agora aguarde o feedback do professor.',
            default => $progressoAtual->proximo_passo ?: 'Revise o feedback e prepare-se para a próxima missão.',
        };

        return view('dashboard', [
            'jornada' => [
                'equipe' => $equipe,
                'categoria' => Categoria::paraPontuacao($equipe->xp_total),
                'missoes' => $missoes,
                'missaoAtual' => $missaoAtual,
                'progressoAtual' => $progressoAtual,
                'ultimoFeedback' => $ultimoFeedback,
                'concluidas' => $concluidas,
                'proximaAcao' => $proximaAcao,
            ],
        ]);
    }
}
