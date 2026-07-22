<?php

namespace App\Providers;

use App\Models\Badge;
use App\Models\Categoria;
use App\Models\Certificado;
use App\Models\Equipe;
use App\Models\Missao;
use App\Models\Turma;
use App\Models\User;
use App\Policies\AlunoPolicy;
use App\Policies\BadgePolicy;
use App\Policies\CategoriaPolicy;
use App\Policies\CertificadoPolicy;
use App\Policies\EquipePolicy;
use App\Policies\MissaoPolicy;
use App\Policies\TurmaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(User::class, AlunoPolicy::class);
        Gate::policy(Badge::class, BadgePolicy::class);
        Gate::policy(Equipe::class, EquipePolicy::class);
        Gate::policy(Turma::class, TurmaPolicy::class);
        Gate::policy(Categoria::class, CategoriaPolicy::class);
        Gate::policy(Certificado::class, CertificadoPolicy::class);
        Gate::policy(Missao::class, MissaoPolicy::class);
    }
}
