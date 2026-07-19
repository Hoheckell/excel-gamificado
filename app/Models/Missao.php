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
        'descricao',
        'pontuacao',
    ];

    protected $casts = [
        'pontuacao' => 'integer',
    ];

    public function equipes(): BelongsToMany
    {
        return $this->belongsToMany(Equipe::class, 'equipes_missoes')
                    ->using(EquipeMissao::class)
                    ->withTimestamps();
    }

    public function progresso(): HasMany
    {
        return $this->hasMany(EquipeMissaoUser::class, 'missao_id');
    }
}
