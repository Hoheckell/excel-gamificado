<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
                ->using(EquipeMissao::class) // Usa nosso Model Pivot personalizado
                ->withTimestamps();
}
}
