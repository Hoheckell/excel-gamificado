<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    protected $fillable = [
        'user_id', 'turma_id', 'equipe_id', 'categoria_id',
        'nome_aluno', 'cpf_aluno',
        'nome_equipe', 'nome_categoria', 'titulo_certificado',
        'cpf_professor', 'nome_professor',
        'dt_inicio', 'dt_fim', 'dt_ultima_aula',
        'codigo_validacao', 'emitido_em',
    ];

    protected $casts = [
        'dt_inicio' => 'date',
        'dt_fim' => 'date',
        'dt_ultima_aula' => 'date',
        'emitido_em' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class);
    }

    public function equipe(): BelongsTo
    {
        return $this->belongsTo(Equipe::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function getQrCodeUrlAttribute(): string
    {
        $url = route('certificados.validar', $this->codigo_validacao);
        return 'https://quickchart.io/qr?text=' . urlencode($url) . '&size=150&margin=2';
    }
}
