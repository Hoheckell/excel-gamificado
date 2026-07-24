<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\EquipeMissao;
use App\Models\EquipeMissaoUser;
use App\Models\Missao;
use App\Models\MissaoAnexo;
use App\Rules\MissionAttachment;
use App\Support\MissionHtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MissaoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Missao::class, 'missao');
    }

    public function index(MissionHtmlSanitizer $sanitizer): View
    {
        $missoes = Missao::with(['equipes:id,nome', 'equipes.alunos:id,name,equipe_id', 'progresso.papeis'])
            ->withCount('equipes')
            ->orderBy('ordem')
            ->paginate(15);

        $missoes->getCollection()->each(function (Missao $missao) use ($sanitizer): void {
            $missao->setAttribute(
                'descricao_preview_html',
                $sanitizer->sanitize($missao->descricao)
            );
        });

        return view('missoes.index', compact('missoes'));
    }

    public function create(): View
    {
        return view('missoes.create');
    }

    public function store(Request $request, MissionHtmlSanitizer $sanitizer): RedirectResponse
    {
        $validated = $this->validateMission($request);
        $files = $request->file('anexos', []);

        $validated['descricao'] = $sanitizer->sanitize($validated['descricao']);
        $validated['permite_resposta'] = $request->boolean('permite_resposta');
        $validated['permite_anexo'] = $request->boolean('permite_anexo');
        unset($validated['anexos']);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($validated, $files, &$storedPaths): void {
                $missao = Missao::create($validated);
                $this->storeAttachments($missao, $files, $storedPaths);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);
            throw $exception;
        }

        return redirect()->route('missoes.index')
            ->with('success', 'Missão criada com sucesso.');
    }

    private function validateMission(Request $request, ?Missao $missao = null): array
    {
        return $request->validate([
            'titulo' => 'required|string|max:255',
            'ordem' => 'required|integer|min:1',
            'descricao' => 'required|string|max:20000',
            'url' => ['nullable', 'url:http,https', 'max:2048'],
            'pontuacao' => 'required|integer|min:1|max:500',
            'permite_resposta' => 'sometimes|boolean',
            'permite_anexo' => 'sometimes|boolean',
            'anexos' => ['sometimes', 'array'],
            'anexos.*' => ['file', new MissionAttachment],
            'remover_anexos' => ['sometimes', 'array'],
            'remover_anexos.*' => [
                'integer',
                Rule::exists('missao_anexos', 'id')->when(
                    $missao,
                    fn ($query) => $query->where('missao_id', $missao->id)
                ),
            ],
        ]);
    }

    public function show(Missao $missao): View
    {
        $missao->load(['equipes:id,nome,turma_id', 'anexos']);

        return view('missoes.show', compact('missao'));
    }

    public function edit(Missao $missao): View
    {
        $missao->load('anexos');

        return view('missoes.edit', compact('missao'));
    }

    public function update(Request $request, Missao $missao, MissionHtmlSanitizer $sanitizer): RedirectResponse
    {
        $validated = $this->validateMission($request, $missao);
        $files = $request->file('anexos', []);
        $removeIds = $validated['remover_anexos'] ?? [];
        $validated['descricao'] = $sanitizer->sanitize($validated['descricao']);
        $validated['permite_resposta'] = $request->boolean('permite_resposta');
        $validated['permite_anexo'] = $request->boolean('permite_anexo');
        unset($validated['anexos'], $validated['remover_anexos']);

        $storedPaths = [];
        $pathsToDelete = [];

        try {
            DB::transaction(function () use ($missao, $validated, $files, $removeIds, &$storedPaths, &$pathsToDelete): void {
                $missao->update($validated);
                $attachments = $missao->anexos()->whereKey($removeIds)->get();
                $pathsToDelete = $attachments->pluck('path')->all();
                $attachments->each->delete();
                $this->storeAttachments($missao, $files, $storedPaths);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);
            throw $exception;
        }

        Storage::disk('local')->delete($pathsToDelete);

        return redirect()->route('missoes.index')
            ->with('success', 'Missão atualizada.');
    }

    public function destroy(Missao $missao): RedirectResponse
    {
        $paths = $missao->anexos()->pluck('path')->all();
        $missao->delete();
        Storage::disk('local')->delete($paths);

        return redirect()->route('missoes.index')
            ->with('success', 'Missão removida.');
    }

    public function baixarRecurso(Missao $missao, MissaoAnexo $anexo): Response
    {
        $this->authorize('view', $missao);
        abort_unless($anexo->missao_id === $missao->id, 404);
        if ($anexo->removido_em) {
            return response('O arquivo não existe porque a turma foi concluída.', 410);
        }
        abort_unless(Storage::disk('local')->exists($anexo->path), 404);

        return Storage::disk('local')->download(
            $anexo->path,
            $anexo->nome_original,
            ['Content-Type' => $anexo->mime_type, 'X-Content-Type-Options' => 'nosniff']
        );
    }

    private function storeAttachments(Missao $missao, array $files, array &$storedPaths): void
    {
        foreach ($files as $file) {
            $path = $file->store("anexos-missoes/{$missao->id}", 'local');
            $storedPaths[] = $path;
            $missao->anexos()->create([
                'path' => $path,
                'nome_original' => preg_replace(
                    '/[\x00-\x1F\x7F]/u',
                    '',
                    basename($file->getClientOriginalName())
                ),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'tamanho' => $file->getSize(),
            ]);
        }
    }

    public function atribuir(Request $request, Missao $missao): RedirectResponse
    {
        $this->authorize('atribuirEquipes', $missao);

        $validated = $request->validate([
            'equipe_ids' => 'required|array|min:1',
            'equipe_ids.*' => 'exists:equipes,id',
        ]);

        $missao->equipes()->syncWithoutDetaching($validated['equipe_ids']);

        return back()->with('success', 'Equipes vinculadas à missão.');
    }

    public function removerEquipe(Request $request, Missao $missao): RedirectResponse
    {
        $this->authorize('atribuirEquipes', $missao);

        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
        ]);

        $missao->equipes()->detach($validated['equipe_id']);

        return back()->with('success', 'Equipe removida da missão.');
    }

    // ─── Controle de Missão pelo Aluno ─────────────────────────────

    public function iniciar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
            'perfis' => 'required|array',
            'perfis.*' => 'required|string',
        ]);

        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validated['equipe_id']) {
            abort(403);
        }

        $equipe = Equipe::with('alunos:id,equipe_id')->findOrFail($validated['equipe_id']);
        $ausentes = EquipeMissaoUser::where('equipe_id', $equipe->id)
            ->where('missao_id', $validated['missao_id'])
            ->where('status', 'ausente')
            ->pluck('user_id');
        $membros = $equipe->alunos->whereNotIn('id', $ausentes)
            ->pluck('id')->map(fn ($id) => (string) $id)->sort()->values();
        $informados = collect(array_keys($validated['perfis']))->map(fn ($id) => (string) $id)->sort()->values();

        if ($membros->isEmpty()) {
            return back()->withErrors(['perfis' => 'A missão precisa ter pelo menos um membro presente.']);
        }

        if ($membros->values()->all() !== $informados->values()->all()) {
            return back()->withErrors(['perfis' => 'Defina um perfil para todos os membros presentes da equipe.']);
        }

        $missao = Missao::findOrFail($validated['missao_id']);
        if (! $equipe->missoes()->whereKey($missao->id)->exists()) {
            abort(403);
        }

        $quantidadePresentes = $membros->count();
        $pacotesDisponiveis = EquipeMissaoUser::PERFIS_MULTICLASSE[$quantidadePresentes] ?? null;
        if (! $pacotesDisponiveis) {
            return back()->withErrors([
                'perfis' => 'A distribuição automática aceita equipes com até quatro integrantes presentes.',
            ]);
        }

        $perfisInformados = collect($validated['perfis'])->values()->sort()->values();
        $perfisObrigatorios = collect(array_keys($pacotesDisponiveis))->sort()->values();
        if ($perfisInformados->all() !== $perfisObrigatorios->all()) {
            return back()->withErrors([
                'perfis' => 'Distribua cada perfil multiclasse exatamente uma vez para cobrir os quatro papéis.',
            ]);
        }

        $distribuicaoAtual = collect($validated['perfis'])
            ->mapWithKeys(fn ($perfil, $userId) => [
                (int) $userId => collect($pacotesDisponiveis[$perfil])->sort()->values()->all(),
            ]);
        $papeisCobertos = $distribuicaoAtual->flatten()->unique()->sort()->values();
        if ($papeisCobertos->all() !== collect(array_keys(EquipeMissaoUser::PAPEIS))->sort()->values()->all()) {
            return back()->withErrors(['perfis' => 'Todos os quatro papéis precisam estar cobertos na rodada.']);
        }

        $missaoAnterior = Missao::where('ordem', $missao->ordem - 1)->first();

        if ($missaoAnterior) {
            $progressosAnteriores = EquipeMissaoUser::where('equipe_id', $equipe->id)
                ->where('missao_id', $missaoAnterior->id)
                ->where('status', '!=', 'ausente')
                ->with('papeis')
                ->get();
            $distribuicaoAnterior = $progressosAnteriores->mapWithKeys(
                fn ($progresso) => [$progresso->user_id => $progresso->papeis_codigos]
            );

            if ($distribuicaoAnterior->count() === $quantidadePresentes && $quantidadePresentes > 1) {
                if ($distribuicaoAnterior->sortKeys()->all() === $distribuicaoAtual->sortKeys()->all()) {
                    return back()->withErrors([
                        'perfis' => 'O rodízio exige uma distribuição diferente da missão anterior.',
                    ]);
                }
            } elseif ($quantidadePresentes > 1) {
                foreach ($distribuicaoAtual as $userId => $papeis) {
                    $anteriores = $distribuicaoAnterior->get($userId);
                    if ($anteriores && $anteriores[0] === $papeis[0]) {
                        return back()->withErrors([
                            'perfis' => 'Como a presença mudou, altere a classe principal dos integrantes que continuam na equipe.',
                        ]);
                    }
                }
            }
        }

        $tempoExtra = $quantidadePresentes <= 3 ? 5 : 0;
        DB::transaction(function () use ($validated, $equipe, $distribuicaoAtual, $tempoExtra): void {
            foreach ($distribuicaoAtual as $userId => $papeis) {
                $progresso = EquipeMissaoUser::updateOrCreate(
                    [
                        'equipe_id' => $equipe->id,
                        'missao_id' => $validated['missao_id'],
                        'user_id' => $userId,
                    ],
                    [
                        'status' => 'em_andamento',
                        'started_at' => now(),
                        'finished_at' => null,
                    ]
                );
                $progresso->papeis()->delete();
                $progresso->papeis()->createMany(
                    collect($papeis)->map(fn ($papel) => ['papel' => $papel])->all()
                );
            }

            EquipeMissao::where('equipe_id', $equipe->id)
                ->where('missao_id', $validated['missao_id'])
                ->firstOrFail()
                ->update(['tempo_extra_minutos' => $tempoExtra]);
        });

        $mensagemTempo = $tempoExtra ? ' A consultoria recebeu 5 minutos extras pela configuração multiclasse.' : '';

        return back()->with('success', 'Missão iniciada com os quatro papéis cobertos!'.$mensagemTempo);
    }

    public function comunicarFalta(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validated['equipe_id']) {
            abort(403);
        }
        if ($user->id == $validated['user_id']) {
            return back()->withErrors(['falta' => 'Você não pode comunicar a própria falta.']);
        }

        $equipe = Equipe::with('alunos:id,equipe_id')->findOrFail($validated['equipe_id']);
        if (! $equipe->alunos->contains('id', $validated['user_id'])) {
            abort(403);
        }
        if (! $equipe->missoes()->whereKey($validated['missao_id'])->exists()) {
            abort(403);
        }

        $ausentesAtuais = EquipeMissaoUser::where('equipe_id', $equipe->id)
            ->where('missao_id', $validated['missao_id'])
            ->where('status', 'ausente')
            ->pluck('user_id');
        if ($equipe->alunos->pluck('id')->diff($ausentesAtuais)->count() <= 1) {
            return back()->withErrors(['falta' => 'A missão precisa manter pelo menos um integrante presente.']);
        }

        $registro = EquipeMissaoUser::where([
            'equipe_id' => $validated['equipe_id'],
            'missao_id' => $validated['missao_id'],
            'user_id' => $validated['user_id'],
        ])->first();
        if ($registro?->status === 'ausente') {
            return back()->withErrors(['falta' => 'A falta deste integrante já foi comunicada e não pode ser desfeita.']);
        }
        if ($registro?->status === 'concluida') {
            return back()->withErrors(['falta' => 'Não é possível comunicar falta para quem já concluiu a missão.']);
        }

        EquipeMissaoUser::updateOrCreate(
            [
                'equipe_id' => $validated['equipe_id'],
                'missao_id' => $validated['missao_id'],
                'user_id' => $validated['user_id'],
            ],
            [
                'status' => 'ausente',
                'started_at' => null,
                'finished_at' => null,
                'pontuacao_obtida' => null,
            ]
        );

        return back()->with('success', 'Falta comunicada. O integrante não bloqueará a conclusão desta missão.');
    }

    public function finalizar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
        ]);

        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validated['equipe_id']) {
            abort(403);
        }

        $registro = EquipeMissaoUser::where([
            'equipe_id' => $validated['equipe_id'],
            'missao_id' => $validated['missao_id'],
            'user_id' => $user->id,
        ])->first();

        if (! $registro || $registro->status !== 'em_andamento') {
            return back()->with('error', 'Missão não está em andamento.');
        }

        $registro->update([
            'status' => 'concluida',
            'finished_at' => now(),
        ]);

        return back()->with('success', 'Missão concluída em '.$registro->duracao.'!');
    }

    public function entregar(Request $request): RedirectResponse
    {
        $validatedIds = $request->validate([
            'equipe_id' => 'required|exists:equipes,id',
            'missao_id' => 'required|exists:missoes,id',
        ]);
        $user = $request->user();
        if (! $user->isAluno() || $user->equipe_id != $validatedIds['equipe_id']) {
            abort(403);
        }

        $missao = Missao::findOrFail($validatedIds['missao_id']);
        $validated = $request->validate([
            'resposta' => [Rule::prohibitedIf(! $missao->permite_resposta), 'nullable', 'string', 'max:5000'],
            'anexo' => [Rule::prohibitedIf(! $missao->permite_anexo), 'nullable', 'file', 'max:10240'],
        ]);
        $equipe = Equipe::with('alunos:id,equipe_id')->findOrFail($validatedIds['equipe_id']);
        if ($equipe->turma?->concluida_em) {
            return back()->withErrors(['entrega' => 'Não é possível enviar arquivos porque a turma foi concluída.']);
        }
        $ausentes = EquipeMissaoUser::where($validatedIds)
            ->where('status', 'ausente')
            ->pluck('user_id');
        $membrosAtivos = $equipe->alunos->whereNotIn('id', $ausentes)->pluck('id');
        $concluidos = EquipeMissaoUser::where($validatedIds)
            ->whereIn('user_id', $membrosAtivos)
            ->where('status', 'concluida')
            ->count();
        if ($membrosAtivos->isEmpty() || $concluidos !== $membrosAtivos->count()) {
            return back()->withErrors(['entrega' => 'A entrega só é liberada após todos os membros concluírem a missão.']);
        }

        $entrega = EquipeMissao::where($validatedIds)->firstOrFail();
        $reenvioPendente = $entrega->reenvio_solicitado_em && ! $entrega->reenvio_entregue_em;
        $reformulacaoPendente = $entrega->reformulacao_solicitada_em && ! $entrega->reformulacao_entregue_em;
        $avaliacaoRegistrada = EquipeMissaoUser::where($validatedIds)
            ->whereNotNull('pontuacao_obtida')
            ->exists();
        $editandoResposta = ! $request->hasFile('anexo')
            && $missao->permite_resposta
            && array_key_exists('resposta', $validated);

        if ($entrega->anexo_path && ! $reenvioPendente && ! $editandoResposta) {
            return back()->withErrors(['entrega' => 'A entrega já foi enviada. Um novo anexo depende de solicitação do professor.']);
        }
        if (($entrega->resposta || $entrega->anexo_path) && $editandoResposta && $avaliacaoRegistrada && ! $reformulacaoPendente) {
            return back()->withErrors(['resposta' => 'A resposta textual não pode mais ser editada após a avaliação do professor.']);
        }
        if ($reenvioPendente && ! $request->hasFile('anexo')) {
            return back()->withErrors(['anexo' => 'Selecione o novo anexo solicitado pelo professor.']);
        }
        if ($reformulacaoPendente && blank($validated['resposta'] ?? null)) {
            return back()->withErrors(['resposta' => 'Escreva a resposta reformulada solicitada pelo professor.']);
        }

        $dados = [];
        if ((! $avaliacaoRegistrada || $reformulacaoPendente) && array_key_exists('resposta', $validated)) {
            $dados['resposta'] = $validated['resposta'];
            if ($reformulacaoPendente) {
                $dados['reformulacao_entregue_em'] = now();
            }
        }
        if ($request->hasFile('anexo')) {
            $anexoAnterior = $entrega->anexo_path;
            $dados['anexo_path'] = $request->file('anexo')->store('anexos-missoes');
            $dados['anexo_nome_original'] = $request->file('anexo')->getClientOriginalName();
            if ($reenvioPendente) {
                $dados['reenvio_entregue_em'] = now();
            }
        }
        $entrega->update($dados);
        if (isset($anexoAnterior) && $anexoAnterior !== $entrega->anexo_path) {
            Storage::delete($anexoAnterior);
        }

        return back()->with('success', match (true) {
            $reenvioPendente => 'Novo anexo enviado com sucesso. A pontuação atual foi preservada e o professor poderá reavaliar a entrega.',
            $reformulacaoPendente => 'Resposta reformulada com sucesso. A pontuação atual foi preservada e o professor poderá reavaliá-la.',
            $editandoResposta && ($entrega->resposta || $entrega->anexo_path) => 'Resposta textual atualizada com sucesso.',
            default => 'Entrega da equipe enviada com sucesso.',
        });
    }

    public function baixarAnexo(Request $request, EquipeMissao $entrega): Response
    {
        if ($request->user()->equipe_id !== $entrega->equipe_id) {
            $this->authorize('atribuirEquipes', $entrega->missao);
        }

        if ($entrega->anexo_removido_em || $entrega->equipe->turma?->concluida_em) {
            return response('O arquivo não existe porque a turma foi concluída.', 410);
        }

        abort_unless($entrega->anexo_path && Storage::exists($entrega->anexo_path), 404);

        return Storage::download($entrega->anexo_path, $entrega->anexo_nome_original);
    }

    public function solicitarReenvio(Request $request, EquipeMissao $entrega): RedirectResponse
    {
        $validated = $request->validate([
            'feedback_reenvio' => 'required|string|max:2000',
        ]);

        $this->authorize('atribuirEquipes', $entrega->missao);
        if ($entrega->equipe->turma?->concluida_em) {
            return back()->withErrors(['reenvio' => 'Não é possível solicitar reenvio porque a turma foi concluída.']);
        }
        if (! $entrega->missao->permite_anexo || ! $entrega->anexo_path) {
            return back()->withErrors(['reenvio' => 'A equipe precisa ter enviado um anexo antes da solicitação de reenvio.']);
        }

        $ausentes = EquipeMissaoUser::where('equipe_id', $entrega->equipe_id)
            ->where('missao_id', $entrega->missao_id)
            ->where('status', 'ausente')
            ->pluck('user_id');
        $membrosPresentes = $entrega->equipe->alunos->whereNotIn('id', $ausentes)->pluck('id');
        $concluidos = EquipeMissaoUser::where('equipe_id', $entrega->equipe_id)
            ->where('missao_id', $entrega->missao_id)
            ->whereIn('user_id', $membrosPresentes)
            ->where('status', 'concluida')
            ->count();
        if ($membrosPresentes->isEmpty() || $concluidos !== $membrosPresentes->count()) {
            return back()->withErrors(['reenvio' => 'O reenvio só pode ser solicitado após a missão ser concluída pela equipe.']);
        }

        $entrega->update([
            'feedback_reenvio' => $validated['feedback_reenvio'],
            'reenvio_solicitado_em' => now(),
            'reenvio_entregue_em' => null,
        ]);

        return back()->with('success', 'Reenvio do anexo solicitado sem alterar a pontuação.');
    }

    public function solicitarReformulacao(Request $request, EquipeMissao $entrega): RedirectResponse
    {
        $validated = $request->validate([
            'feedback_reformulacao' => 'required|string|max:2000',
        ]);

        $this->authorize('atribuirEquipes', $entrega->missao);
        if ($entrega->equipe->turma?->concluida_em) {
            return back()->withErrors(['reformulacao' => 'Não é possível solicitar reformulação porque a turma foi concluída.']);
        }
        if (! $entrega->missao->permite_resposta || $entrega->missao->permite_anexo || blank($entrega->resposta)) {
            return back()->withErrors(['reformulacao' => 'A reformulação está disponível somente para missões textuais sem anexo e com resposta enviada.']);
        }

        $ausentes = EquipeMissaoUser::where('equipe_id', $entrega->equipe_id)
            ->where('missao_id', $entrega->missao_id)
            ->where('status', 'ausente')
            ->pluck('user_id');
        $membrosPresentes = $entrega->equipe->alunos->whereNotIn('id', $ausentes)->pluck('id');
        $concluidos = EquipeMissaoUser::where('equipe_id', $entrega->equipe_id)
            ->where('missao_id', $entrega->missao_id)
            ->whereIn('user_id', $membrosPresentes)
            ->where('status', 'concluida')
            ->count();
        if ($membrosPresentes->isEmpty() || $concluidos !== $membrosPresentes->count()) {
            return back()->withErrors(['reformulacao' => 'A reformulação só pode ser solicitada após a missão ser concluída pela equipe.']);
        }

        $entrega->update([
            'feedback_reformulacao' => $validated['feedback_reformulacao'],
            'reformulacao_solicitada_em' => now(),
            'reformulacao_entregue_em' => null,
        ]);

        return back()->with('success', 'Reformulação da resposta solicitada sem alterar a pontuação.');
    }

    public function pontuar(Request $request): RedirectResponse
    {
        $niveis = EquipeMissaoUser::NIVEIS_COMPETENCIA;
        $validated = $request->validate([
            'registro_id' => 'required|exists:equipe_missao_user,id',
            'pontuacao' => 'required|integer|min:0',
            'competencia_formulas' => ['required', Rule::in($niveis)],
            'competencia_qualidade' => ['required', Rule::in($niveis)],
            'competencia_visual' => ['required', Rule::in($niveis)],
            'competencia_colaboracao' => ['required', Rule::in($niveis)],
            'feedback_professor' => 'nullable|string|max:2000',
            'proximo_passo' => 'nullable|string|max:1000',
        ]);

        $registro = EquipeMissaoUser::findOrFail($validated['registro_id']);
        $this->authorize('atribuirEquipes', $registro->missao);
        if ($validated['pontuacao'] > $registro->missao->pontuacao) {
            return back()->withErrors(['pontuacao' => 'A pontuação não pode superar o valor da missão.']);
        }
        $precisaEvoluir = collect(EquipeMissaoUser::COMPETENCIAS)
            ->keys()
            ->contains(fn ($campo) => $validated[$campo] !== 'dominado');
        if ($precisaEvoluir && blank($validated['proximo_passo'] ?? null)) {
            return back()->withErrors([
                'proximo_passo' => 'Indique um próximo passo quando alguma competência ainda não foi dominada.',
            ]);
        }

        $dadosAvaliacao = collect($validated)
            ->except(['registro_id', 'pontuacao'])
            ->all();
        $dadosAvaliacao['pontuacao_obtida'] = $validated['pontuacao'];
        $registro->update($dadosAvaliacao);

        return back()->with('success', 'Avaliação e próximo passo registrados.');
    }
}
