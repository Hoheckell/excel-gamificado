<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipeMissaoUser extends Model
{
    public const NIVEIS_COMPETENCIA = [
        'precisa_praticar',
        'em_desenvolvimento',
        'dominado',
    ];

    public const COMPETENCIAS = [
        'competencia_formulas' => 'Fórmulas e lógica',
        'competencia_qualidade' => 'Qualidade e conferência',
        'competencia_visual' => 'Comunicação visual',
        'competencia_colaboracao' => 'Colaboração e responsabilidade',
    ];

    protected $table = 'equipe_missao_user';

    public $timestamps = true;

    protected $fillable = [
        'equipe_id', 'missao_id', 'user_id',
        'papel', 'status', 'started_at', 'finished_at', 'pontuacao_obtida',
        'competencia_formulas', 'competencia_qualidade', 'competencia_visual',
        'competencia_colaboracao', 'feedback_professor', 'proximo_passo',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'pontuacao_obtida' => 'integer',
    ];

    public function equipe(): BelongsTo
    {
        return $this->belongsTo(Equipe::class);
    }

    public function missao(): BelongsTo
    {
        return $this->belongsTo(Missao::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDuracaoAttribute(): ?string
    {
        if (! $this->started_at || ! $this->finished_at) {
            return null;
        }
        $diff = $this->started_at->diffInSeconds($this->finished_at);

        return sprintf('%d:%02d:%02d', floor($diff / 3600), floor(($diff % 3600) / 60), $diff % 60);
    }

    public function getDuracaoSegundosAttribute(): ?int
    {
        if (! $this->started_at || ! $this->finished_at) {
            return null;
        }

        return $this->started_at->diffInSeconds($this->finished_at);
    }

    public function getTemFeedbackAttribute(): bool
    {
        return $this->pontuacao_obtida !== null
            || filled($this->feedback_professor)
            || collect(self::COMPETENCIAS)->keys()->contains(fn ($campo) => filled($this->{$campo}));
    }
}
