<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Missao extends Model
{
    use HasFactory;

    protected $table = 'missoes';

    protected $fillable = [
        'titulo',
        'ordem',
        'descricao',
        'pontuacao',
        'permite_resposta',
        'permite_anexo',
    ];

    protected $casts = [
        'ordem' => 'integer',
        'pontuacao' => 'integer',
        'permite_resposta' => 'boolean',
        'permite_anexo' => 'boolean',
    ];

    public function equipes(): BelongsToMany
    {
        return $this->belongsToMany(Equipe::class, 'equipes_missoes')
            ->using(EquipeMissao::class)
            ->withPivot(['id', 'resposta', 'anexo_path', 'anexo_nome_original', 'tempo_extra_minutos'])
            ->withTimestamps();
    }

    public function progresso(): HasMany
    {
        return $this->hasMany(EquipeMissaoUser::class, 'missao_id');
    }
}
