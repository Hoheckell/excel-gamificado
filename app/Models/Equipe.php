<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'turma_id',
        'nome',
        'pontuacao',
    ];

    protected $casts = [
        'pontuacao' => 'integer',
    ];

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function alunos(): HasMany
    {
        return $this->hasMany(User::class, 'equipe_id');
    }

    public function missoes(): BelongsToMany
    {
        return $this->belongsToMany(Missao::class, 'equipes_missoes')
            ->using(EquipeMissao::class)
            ->withPivot(['id', 'resposta', 'anexo_path', 'anexo_nome_original'])
            ->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'equipe_badge')->withTimestamps();
    }

    public function progressos(): HasMany
    {
        return $this->hasMany(EquipeMissaoUser::class);
    }

    public function getXpTotalAttribute(): int
    {
        $xpMissoes = $this->progressos()
            ->whereNotNull('pontuacao_obtida')
            ->selectRaw('missao_id, MAX(pontuacao_obtida) as pontos')
            ->groupBy('missao_id')
            ->get()
            ->sum('pontos');

        $xpBadges = $this->badges()->sum('pontos_bonus');

        return max(0, (int) $this->pontuacao) + (int) $xpMissoes + (int) $xpBadges;
    }
}
