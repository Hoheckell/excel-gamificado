<?php

namespace App\Services;

use App\Models\EquipeMissao;
use App\Models\MissaoAnexo;
use App\Models\Turma;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ConcluirTurma
{
    public function execute(Turma $turma): void
    {
        if ($turma->concluida_em) {
            return;
        }

        $equipeIds = $turma->equipes()->pluck('id');
        $entregas = EquipeMissao::query()
            ->whereIn('equipe_id', $equipeIds)
            ->get();
        $missaoIds = $entregas->pluck('missao_id')->unique();
        $anexosMissoes = MissaoAnexo::query()
            ->whereIn('missao_id', $missaoIds)
            ->whereNull('removido_em')
            ->get();

        $paths = $entregas
            ->whereNull('anexo_removido_em')
            ->pluck('anexo_path')
            ->filter()
            ->merge($anexosMissoes->pluck('path'))
            ->unique()
            ->values()
            ->all();

        Storage::disk('local')->delete($paths);

        if (collect($paths)->contains(fn (string $path): bool => Storage::disk('local')->exists($path))) {
            throw new RuntimeException('Não foi possível apagar todos os anexos da turma.');
        }

        DB::transaction(function () use ($turma, $equipeIds, $anexosMissoes): void {
            $concluidaEm = now();

            EquipeMissao::query()
                ->whereIn('equipe_id', $equipeIds)
                ->whereNotNull('anexo_path')
                ->whereNull('anexo_removido_em')
                ->update(['anexo_removido_em' => $concluidaEm]);

            MissaoAnexo::query()
                ->whereKey($anexosMissoes->modelKeys())
                ->update(['removido_em' => $concluidaEm]);

            $turma->update(['concluida_em' => $concluidaEm]);
        });
    }
}
