<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'pt_classificacao',
        'descricao',
        'titulo_certificado',
        'cor',
    ];

    protected $casts = [
        'pt_classificacao' => 'integer',
    ];

    /**
     * Faixa de pontuação superior (calculada a partir da próxima categoria).
     * Retorna null se for a última (categoria mais alta).
     */
    public function getPtLimiteSuperiorAttribute(): ?int
    {
        $proxima = self::where('pt_classificacao', '>', $this->pt_classificacao)
            ->orderBy('pt_classificacao', 'asc')
            ->first();

        return $proxima ? $proxima->pt_classificacao - 1 : 500;
    }

    /**
     * Determina a categoria de uma equipe com base na sua pontuação.
     */
    public static function paraPontuacao(int $pontuacao): ?self
    {
        return self::where('pt_classificacao', '<=', $pontuacao)
            ->orderBy('pt_classificacao', 'desc')
            ->first();
    }
}
