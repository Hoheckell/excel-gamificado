<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\MissaoController;
use App\Http\Controllers\PlacarController;
use App\Http\Controllers\SorteioController;
use App\Http\Controllers\TurmaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/certificados/validar/{codigo}', [CertificadoController::class, 'validar'])->name('certificados.validar');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('alunos', AlunoController::class)->except(['show']);
    Route::resource('equipes', EquipeController::class);
    Route::resource('turmas', TurmaController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('missoes', MissaoController::class);

    Route::post('missoes/{missao}/atribuir', [MissaoController::class, 'atribuir'])->name('missoes.atribuir');
    Route::post('missoes/{missao}/remover-equipe', [MissaoController::class, 'removerEquipe'])->name('missoes.removerEquipe');
    Route::post('missoes/iniciar', [MissaoController::class, 'iniciar'])->name('missoes.iniciar');
    Route::post('missoes/finalizar', [MissaoController::class, 'finalizar'])->name('missoes.finalizar');
    Route::post('missoes/pontuar', [MissaoController::class, 'pontuar'])->name('missoes.pontuar');

    Route::post('equipes/{equipe}/add-points', [EquipeController::class, 'addPoints'])->name('equipes.addPoints');
    Route::post('equipes/{equipe}/remove-points', [EquipeController::class, 'removePoints'])->name('equipes.removePoints');
    Route::post('equipes/{equipe}/add-aluno', [EquipeController::class, 'addAluno'])->name('equipes.addAluno');
    Route::post('equipes/{equipe}/remove-aluno', [EquipeController::class, 'removeAluno'])->name('equipes.removeAluno');
    Route::post('equipes/criar-por-aluno', [EquipeController::class, 'criarPorAluno'])->name('equipes.criarPorAluno');
    Route::post('equipes/sair', [EquipeController::class, 'sairDaEquipe'])->name('equipes.sair');

    Route::get('sorteio', [SorteioController::class, 'create'])->name('sorteio.create');
    Route::post('sorteio/sortear', [SorteioController::class, 'sortear'])->name('sorteio.sortear');
    Route::post('sorteio/concluir', [SorteioController::class, 'concluir'])->name('sorteio.concluir');

    Route::get('placar', [PlacarController::class, 'index'])->name('placar.index');

    Route::get('/regras', function () {
        return view('regras');
    })->name('regras');

    Route::get('/certificado-modelo', function () {
        return view('certificado-modelo');
    })->name('certificado.modelo');

    Route::get('/certificados/emitir', [CertificadoController::class, 'emitir'])->name('certificados.emitir');
    Route::post('/certificados', [CertificadoController::class, 'store'])->name('certificados.store');
    Route::get('/certificados/{certificado}', [CertificadoController::class, 'confirmacao'])->name('certificados.confirmacao');
    Route::post('/alunos/{aluno}/autorizar', [AlunoController::class, 'autorizar'])->name('alunos.autorizar');
    Route::post('/alunos/{aluno}/reenviar-certificado', [AlunoController::class, 'reenviarCertificado'])->name('alunos.reenviarCertificado');
    Route::post('/turmas/entrar', [AlunoController::class, 'entrarTurma'])->name('turmas.entrar');
});
