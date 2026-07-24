<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissaoAnexo extends Model
{
    protected $table = 'missao_anexos';

    protected $fillable = [
        'missao_id',
        'path',
        'nome_original',
        'mime_type',
        'tamanho',
        'removido_em',
    ];

    protected $casts = [
        'tamanho' => 'integer',
        'removido_em' => 'datetime',
    ];

    public function missao(): BelongsTo
    {
        return $this->belongsTo(Missao::class);
    }
}
