<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EquipeMissao extends Pivot
{
    // Define explicitamente o nome da tabela
    protected $table = 'equipes_missoes';

    // Indica que a tabela possui um ID autoincrementável próprio
    public $incrementing = true;

    protected $fillable = [
        'equipe_id',
        'missao_id',
        'resposta',
        'anexo_path',
        'anexo_nome_original',
        'anexo_removido_em',
        'tempo_extra_minutos',
    ];

    protected $casts = [
        'tempo_extra_minutos' => 'integer',
        'anexo_removido_em' => 'datetime',
    ];

    public function equipe()
    {
        return $this->belongsTo(Equipe::class);
    }

    public function missao()
    {
        return $this->belongsTo(Missao::class);
    }
}
