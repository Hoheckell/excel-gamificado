<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipeMissaoUserPapel extends Model
{
    protected $table = 'equipe_missao_user_papeis';

    protected $fillable = [
        'equipe_missao_user_id',
        'papel',
    ];

    public function progresso(): BelongsTo
    {
        return $this->belongsTo(EquipeMissaoUser::class, 'equipe_missao_user_id');
    }
}
