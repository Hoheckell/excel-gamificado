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
    ];
}