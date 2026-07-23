<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo',
        'equipe_id',
        'autorizado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'autorizado' => 'boolean',
        ];
    }

    public function turmas(): BelongsToMany
    {
        return $this->belongsToMany(Turma::class, 'turmas_users')
            ->withTimestamps();
    }

    public function equipe(): BelongsTo
    {
        return $this->belongsTo(Equipe::class);
    }

    public function isProfessor(): bool
    {
        return $this->tipo === 'professor';
    }

    public function isAluno(): bool
    {
        return $this->tipo === 'aluno';
    }

    public function hasActiveTurma(): bool
    {
        return $this->turmas()->whereDate('dt_fim', '>=', now())->exists();
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    /**
     * Turmas nas quais este usuário é professor (dono/criador).
     * Assumindo que apenas professores criam turmas e o primeiro registro
     * em turmas_users define o professor da turma.
     * Como não há campo 'criador', usamos o tipo do usuário para filtrar.
     */
    public function turmasProfessor(): BelongsToMany
    {
        return $this->turmas();
    }
}
