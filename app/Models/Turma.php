<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descricao',
        'dt_inicio',
        'dt_fim',
        'concluida_em',
    ];

    protected $casts = [
        'dt_inicio' => 'date',
        'dt_fim' => 'date',
        'concluida_em' => 'datetime',
    ];

    public function equipes(): HasMany
    {
        return $this->hasMany(Equipe::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'turmas_users')
            ->withTimestamps();
    }

    public function professores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'turmas_users')
            ->where('users.tipo', 'professor')
            ->withTimestamps();
    }

    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'turmas_users')
            ->where('users.tipo', 'aluno')
            ->withTimestamps();
    }
}
