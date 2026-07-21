<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'icone', 'descricao', 'pontos_bonus'];

    protected $casts = ['pontos_bonus' => 'integer'];

    public function equipes(): BelongsToMany
    {
        return $this->belongsToMany(Equipe::class, 'equipe_badge')->withTimestamps();
    }
}
