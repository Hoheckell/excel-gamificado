<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Equipes</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Listagem de Equipes</p>
            </div>
            @can('create', App\Models\Equipe::class)
                <div class="flex items-center gap-3">
                    <a href="{{ route('sorteio.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.45 20 9.5V4h-5.5zm.33 11.65l-1.41-1.41 3.13-3.13 1.41 1.41-3.13 3.13z"/></svg>
                        Sorteio
                    </a>
                    <a href="{{ route('equipes.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        Nova Equipe
                    </a>
                </div>
            @endcan
            @if (auth()->user()->isAluno() && !auth()->user()->equipe_id)
                <button onclick="document.getElementById('criarEquipeAlunoModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Criar Minha Equipe
                </button>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-6 px-5 py-3 bg-excel-tint border border-excel-light text-excel-dark rounded-excel text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any() || session('error'))
            <div class="mb-6 px-5 py-3 bg-red-50 border border-red-200 text-red-700 rounded-excel text-sm">
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        {{-- Filtros --}}
        <div class="portal-container mb-6">
            <div class="bg-white border-b border-[--border-light] px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <select name="turma_id" onchange="this.form.submit()" class="border border-[--border-light] rounded-excel px-3 py-2 text-sm text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light">
                        <option value="">Todas as Turmas</option>
                        @foreach ($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>
                                {{ $turma->codigo }} - {{ $turma->descricao }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar equipe..." class="border border-[--border-light] rounded-excel px-3 py-2 text-sm text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light w-48">
                    <button type="submit" class="btn-excel text-xs px-4 py-2">Filtrar</button>
                </form>
                <span class="text-sm text-[--text-muted]">{{ $equipes->total() }} equipes</span>
            </div>
        </div>

        {{-- Grid de Equipes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($equipes as $equipe)
                <div class="bg-white border border-[--border-light] rounded-excel overflow-hidden hover:border-excel-light hover:shadow-excel transition-all duration-200">
                    {{-- Cabeçalho da Equipe --}}
                    <div class="bg-[#f8faf8] border-b border-[--border-light] px-5 py-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-[--text-main] text-lg">{{ $equipe->nome }}</h3>
                                <p class="text-xs text-[--text-muted] mt-0.5">
                                    Turma: {{ $equipe->turma->codigo ?? '—' }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-excel-tint text-excel-dark">
                                {{ $equipe->pontuacao }} pts
                            </span>
                        </div>
                    </div>

                    {{-- Alunos da Equipe --}}
                    <div class="px-5 py-3">
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider font-semibold">Alunos ({{ $equipe->alunos->count() }})</span>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @forelse ($equipe->alunos->take(5) as $aluno)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                    <span class="w-4 h-4 rounded-full bg-excel-tint text-excel-dark flex items-center justify-center text-[9px] font-bold">
                                        {{ strtoupper(substr($aluno->name, 0, 1)) }}
                                    </span>
                                    {{ $aluno->name }}
                                </span>
                            @empty
                                <span class="text-xs text-[--text-muted] italic">Nenhum aluno vinculado</span>
                            @endforelse
                            @if ($equipe->alunos->count() > 5)
                                <span class="text-xs text-[--text-muted]">+{{ $equipe->alunos->count() - 5 }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Missões da Equipe (visível para alunos da equipe) --}}
                    @php
                        $user = auth()->user();
                        $mostrarMissoes = ($user->isProfessor() || ($user->isAluno() && $user->equipe_id === $equipe->id)) && $equipe->missoes->isNotEmpty();
                    @endphp
                    @if ($mostrarMissoes)
                        <div class="border-t border-[--border-light]">
                            <button onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                                class="w-full px-5 py-2.5 flex items-center justify-between text-xs font-semibold text-[--text-main] hover:bg-[#f8faf8] transition">
                                <span class="flex items-center gap-1.5">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-excel-dark">
                                        <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6h-5.6z"/>
                                    </svg>
                                    Missões da Equipe ({{ $equipe->missoes->count() }})
                                </span>
                                <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="text-[--text-muted] transition-transform">
                                    <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                                </svg>
                            </button>
                            <div class="hidden px-5 pb-3 space-y-2">
                                @foreach ($equipe->missoes as $missao)
                                    @php
                                        $meuProgresso = $missao->progresso->firstWhere('user_id', $user->id);
                                        $membrosAtivos = $equipe->alunos->pluck('id');
                                        $progressos = $missao->progresso
                                            ->where('equipe_id', $equipe->id)
                                            ->whereIn('user_id', $membrosAtivos);
                                        $ausentes = $progressos->where('status', 'ausente')->pluck('user_id');
                                        $membrosPresentes = $membrosAtivos->diff($ausentes);
                                        $quantidadePresentes = $membrosPresentes->count();
                                        $perfisMulticlasse = match ($quantidadePresentes) {
                                            4 => [
                                                'arquiteto' => '🛠️ Arquiteto de Dados',
                                                'designer' => '🎨 Designer Visual',
                                                'auditor' => '🔍 Auditor de Qualidade',
                                                'gestor' => '⏱️ Gestor de Entregas',
                                            ],
                                            3 => [
                                                'arquiteto' => '🛠️ Arquiteto de Dados',
                                                'designer' => '🎨 Designer Visual',
                                                'controle' => '🔍 Auditor + ⏱️ Gestor',
                                            ],
                                            2 => [
                                                'tecnico' => '🛠️ Arquiteto + 🔍 Auditor (Núcleo Técnico)',
                                                'executivo' => '🎨 Designer + ⏱️ Gestor (Núcleo Executivo)',
                                            ],
                                            1 => [
                                                'senior' => '👑 Consultor Sênior — os quatro papéis',
                                            ],
                                            default => [],
                                        };
                                        $todosConcluiram = $membrosPresentes->isNotEmpty()
                                            && $progressos->whereIn('user_id', $membrosPresentes)->where('status', 'concluida')->count() === $membrosPresentes->count();
                                        $tempoMedio = null;
                                        if ($todosConcluiram) {
                                            $segundos = (int) $progressos->avg(fn($p) => $p->duracao_segundos);
                                            if ($segundos > 0) {
                                                $tempoMedio = sprintf('%d:%02d:%02d', floor($segundos / 3600), floor(($segundos % 3600) / 60), $segundos % 60);
                                            }
                                        }
                                    @endphp
                                    <div class="p-2.5 rounded-excel bg-excel-tint/50 space-y-2">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs text-[--text-main] leading-relaxed">{{ Str::limit(strip_tags($missao->descricao), 120) }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-excel-dark text-white shrink-0">
                                                {{ $missao->pontuacao }}pts
                                            </span>
                                        </div>

                                        {{-- Progresso do aluno --}}
                                        @if ($user->isAluno() && $user->equipe_id === $equipe->id)
                                            <div class="flex items-center gap-2">
                                                @if (! $meuProgresso || $meuProgresso->status === 'pendente')
                                                    <button type="button" onclick="document.getElementById('papeisModal{{ $equipe->id }}_{{ $missao->id }}').classList.remove('hidden')" class="text-[10px] font-bold px-2.5 py-1 rounded bg-excel-dark text-white hover:bg-excel-light transition">
                                                        Definir papéis e iniciar
                                                    </button>

                                                    <div id="papeisModal{{ $equipe->id }}_{{ $missao->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                                        <div class="bg-white rounded-excel w-full max-w-md shadow-xl overflow-hidden text-left">
                                                            <div class="excel-ribbon px-5 py-3">
                                                                <h3 class="text-white font-semibold">Papéis — {{ $missao->titulo }}</h3>
                                                            </div>
                                                            <form method="POST" action="{{ route('missoes.iniciar') }}" class="p-5 space-y-4">
                                                                @csrf
                                                                <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                                                <input type="hidden" name="missao_id" value="{{ $missao->id }}">
                                                                @if ($quantidadePresentes <= 3)
                                                                    <div class="rounded-excel border border-blue-200 bg-blue-50 p-3">
                                                                        <strong class="text-xs text-blue-800">{{ $quantidadePresentes === 1 ? 'Auditoria confidencial — Consultor Sênior' : 'Consultoria Boutique de Alta Performance' }}</strong>
                                                                        <p class="text-[10px] text-blue-700 mt-1">Os quatro pilares continuam cobertos por perfis multiclasse. Esta configuração recebe 5 minutos extras, sem alteração de XP.</p>
                                                                    </div>
                                                                @else
                                                                    <p class="text-xs text-[--text-muted]">Distribua os quatro papéis. Na missão seguinte, altere a combinação da equipe.</p>
                                                                @endif
                                                                @foreach($equipe->alunos->whereNotIn('id', $ausentes) as $membro)
                                                                    <label class="block">
                                                                        <span class="text-xs font-semibold text-[--text-main]">{{ $membro->name }}</span>
                                                                        <select name="perfis[{{ $membro->id }}]" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm bg-white focus:border-excel-dark focus:ring-excel-light">
                                                                            <option value="">Selecione...</option>
                                                                            @foreach ($perfisMulticlasse as $codigoPerfil => $nomePerfil)
                                                                                <option value="{{ $codigoPerfil }}">{{ $nomePerfil }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </label>
                                                                @endforeach
                                                                <div class="flex justify-end gap-3">
                                                                    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button>
                                                                    <x-button>Iniciar para a equipe</x-button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @elseif ($meuProgresso->status === 'em_andamento')
                                                    <form method="POST" action="{{ route('missoes.finalizar') }}" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                                        <input type="hidden" name="missao_id" value="{{ $missao->id }}">
                                                        <button class="text-[10px] font-bold px-2.5 py-1 rounded bg-orange-500 text-white hover:bg-orange-600 transition">
                                                            Finalizar
                                                        </button>
                                                    </form>
                                                    <span class="text-[10px] text-[--text-muted]">Iniciado {{ $meuProgresso->started_at->format('H:i') }}</span>
                                                @elseif ($meuProgresso->status === 'concluida')
                                                    <span class="inline-flex items-center gap-1 text-[10px] text-green-700 font-semibold">
                                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                                        Concluída em {{ $meuProgresso->duracao }}
                                                    </span>
                                                    @if ($meuProgresso->pontuacao_obtida !== null)
                                                        <span class="text-[10px] font-bold text-excel-dark">{{ $meuProgresso->pontuacao_obtida }}/{{ $missao->pontuacao }}pts</span>
                                                    @endif
                                                @elseif ($meuProgresso->status === 'ausente')
                                                    <span class="text-[10px] font-semibold text-red-600">Ausente nesta missão</span>
                                                @endif
                                            </div>
                                            @if ($missao->pivot->tempo_extra_minutos > 0)
                                                <span class="inline-flex text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded px-2 py-1">
                                                    +{{ $missao->pivot->tempo_extra_minutos }} min · Contrato Enxuto
                                                </span>
                                            @endif

                                            @php
                                                $candidatosFalta = $equipe->alunos
                                                    ->where('id', '!=', $user->id)
                                                    ->reject(fn ($membro) => in_array(optional($progressos->firstWhere('user_id', $membro->id))->status, ['ausente', 'concluida']));
                                                $missaoIniciada = $progressos->contains(fn ($p) => in_array($p->status, ['em_andamento', 'concluida']));
                                            @endphp
                                            @if ($missaoIniciada && $candidatosFalta->isNotEmpty())
                                                <button type="button" onclick="document.getElementById('faltaMissaoModal{{ $equipe->id }}_{{ $missao->id }}').classList.remove('hidden')" class="text-[10px] font-semibold text-red-600 underline">Comunicar falta</button>
                                                <div id="faltaMissaoModal{{ $equipe->id }}_{{ $missao->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                                    <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden text-left">
                                                        <div class="bg-red-600 px-5 py-3"><h3 class="text-white font-semibold">Comunicar falta</h3></div>
                                                        <form method="POST" action="{{ route('missoes.comunicarFalta') }}" class="p-5 space-y-4" onsubmit="return confirm('Confirmar esta falta? Esta ação não poderá ser desfeita.')">
                                                            @csrf
                                                            <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                                            <input type="hidden" name="missao_id" value="{{ $missao->id }}">
                                                            <p class="text-xs text-[--text-muted]">Missão: <strong>{{ $missao->titulo }}</strong></p>
                                                            <select name="user_id" required class="block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm bg-white focus:border-excel-dark focus:ring-excel-light">
                                                                <option value="">Selecione o integrante ausente...</option>
                                                                @foreach ($candidatosFalta as $membro)
                                                                    <option value="{{ $membro->id }}">{{ $membro->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <p class="text-xs font-semibold text-red-600">Atenção: esta ação não poderá ser desfeita.</p>
                                                            <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button><x-danger-button>Confirmar falta</x-danger-button></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (! $equipe->turma?->concluida_em && $todosConcluiram && ($missao->permite_resposta || $missao->permite_anexo))
                                                @php
                                                    $reenvioPendente = $missao->pivot->reenvio_solicitado_em && ! $missao->pivot->reenvio_entregue_em;
                                                    $reformulacaoPendente = $missao->pivot->reformulacao_solicitada_em && ! $missao->pivot->reformulacao_entregue_em;
                                                    $avaliacaoRegistrada = $progressos->whereNotNull('pontuacao_obtida')->isNotEmpty();
                                                    $podeEditarResposta = $missao->permite_resposta && $missao->pivot->resposta && ! $avaliacaoRegistrada;
                                                @endphp
                                                @if ((! $missao->pivot->resposta && ! $missao->pivot->anexo_path) || $reenvioPendente || $reformulacaoPendente || $podeEditarResposta)
                                                    <button type="button" onclick="document.getElementById('entregaModal{{ $missao->pivot->id }}').classList.remove('hidden')" class="text-[10px] font-bold px-2.5 py-1 rounded bg-excel-dark text-white hover:bg-excel-light transition">
                                                        {{ $reenvioPendente ? 'Reenviar anexo solicitado' : ($reformulacaoPendente ? 'Reformular resposta solicitada' : ($podeEditarResposta ? 'Editar resposta textual' : 'Enviar entrega da equipe')) }}
                                                    </button>
                                                    <div id="entregaModal{{ $missao->pivot->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                                        <div class="bg-white rounded-excel w-full max-w-md shadow-xl overflow-hidden text-left">
                                                            <div class="excel-ribbon px-5 py-3"><h3 class="text-white font-semibold">Entrega — {{ $missao->titulo }}</h3></div>
                                                            <form method="POST" action="{{ route('missoes.entregar') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                                                                @csrf
                                                                <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                                                <input type="hidden" name="missao_id" value="{{ $missao->id }}">
                                                                @if ($reenvioPendente)
                                                                    <div class="rounded-excel border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                                                                        <strong>Feedback do professor:</strong>
                                                                        <p class="mt-1 whitespace-pre-line">{{ $missao->pivot->feedback_reenvio }}</p>
                                                                        <p class="mt-2 text-xs">Envie somente o novo anexo. A pontuação já registrada não será alterada.</p>
                                                                    </div>
                                                                @endif
                                                                @if ($reformulacaoPendente)
                                                                    <div class="rounded-excel border border-amber-300 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                                                                        <strong>Feedback do professor:</strong>
                                                                        <p class="mt-1 whitespace-pre-line">{{ $missao->pivot->feedback_reformulacao }}</p>
                                                                        <p class="mt-2 text-xs">Reescreva a resposta abaixo. A pontuação já registrada não será alterada automaticamente.</p>
                                                                    </div>
                                                                @endif
                                                                @if ($missao->permite_resposta && (! $avaliacaoRegistrada || $reformulacaoPendente))
                                                                    <div><x-label for="resposta{{ $missao->pivot->id }}" :value="$reformulacaoPendente ? 'Resposta reformulada (obrigatória)' : 'Resposta (opcional)'" /><textarea id="resposta{{ $missao->pivot->id }}" name="resposta" rows="4" maxlength="5000" @required($reformulacaoPendente) class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light" placeholder="Digite a resposta da equipe...">{{ $missao->pivot->resposta }}</textarea></div>
                                                                @endif
                                                                @if ($missao->permite_anexo && (! $missao->pivot->anexo_path || $reenvioPendente))
                                                                    <div><x-label for="anexo{{ $missao->pivot->id }}" :value="$reenvioPendente ? 'Novo anexo (obrigatório, até 10 MB)' : 'Anexo (opcional, até 10 MB)'" /><input id="anexo{{ $missao->pivot->id }}" type="file" name="anexo" @required($reenvioPendente) class="mt-1 block w-full text-xs text-[--text-muted] file:mr-3 file:rounded file:border-0 file:bg-excel-tint file:px-3 file:py-2 file:text-xs file:font-semibold file:text-excel-dark"></div>
                                                                @endif
                                                                <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button><x-button>Enviar entrega</x-button></div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-[10px] font-semibold text-green-700">Entrega da equipe enviada</span>
                                                @endif
                                            @endif
                                        @endif

                                        {{-- Visão do professor: progresso dos alunos + botão pontuar --}}
                                        @if ($user->isProfessor())
                                            <div class="space-y-1">
                                                @foreach ($equipe->alunos as $aluno)
                                                    @php
                                                        $p = $missao->progresso->firstWhere('user_id', $aluno->id);
                                                    @endphp
                                                    <div class="flex items-center justify-between gap-2 text-[10px]">
                                                        <span class="text-[--text-muted] flex items-center gap-1">
                                                            <span class="w-4 h-4 rounded-full bg-excel-tint text-excel-dark flex items-center justify-center text-[8px] font-bold">
                                                                {{ strtoupper(substr($aluno->name, 0, 1)) }}
                                                            </span>
                                                            {{ $aluno->name }}
                                                        </span>
                                                        <span>
                                                            @if ($p && $p->status === 'concluida')
                                                                <span class="text-green-700 font-semibold">{{ $p->duracao }}</span>
                                                                @if ($p->pontuacao_obtida !== null)
                                                                    <span class="text-excel-dark font-bold ml-1">{{ $p->pontuacao_obtida }}pts</span>
                                                                @endif
                                                                <button onclick="document.getElementById('pontuarModal{{ $p->id }}').classList.remove('hidden')"
                                                                    class="text-[10px] text-excel-dark font-bold underline hover:text-excel-light transition">{{ $p->pontuacao_obtida === null ? 'Avaliar' : 'Revisar avaliação' }}</button>
                                                            @elseif ($p && $p->status === 'em_andamento')
                                                                <span class="text-orange-500">Em andamento</span>
                                                            @elseif ($p && $p->status === 'ausente')
                                                                <span class="font-semibold text-red-600">Ausente</span>
                                                            @else
                                                                <span class="text-[--text-muted]">Pendente</span>
                                                            @endif
                                                        </span>
                                                    </div>

                                                    @if ($p && $p->status === 'concluida')
                                                    <div id="pontuarModal{{ $p->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                                        <div class="bg-white rounded-excel w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl">
                                                            <div class="excel-ribbon px-4 py-3">
                                                                <h3 class="text-white text-sm font-semibold">Avaliar aprendizagem — {{ $aluno->name }}</h3>
                                                            </div>
                                                            <form method="POST" action="{{ route('missoes.pontuar') }}" class="p-4 space-y-3">
                                                                @csrf
                                                                <input type="hidden" name="registro_id" value="{{ $p->id }}">
                                                                <div>
                                                                    <span class="text-[10px] text-[--text-muted]">Nota (máx: {{ $missao->pontuacao }} pts)</span>
                                                                    <input type="number" name="pontuacao" value="{{ old('pontuacao', $p->pontuacao_obtida ?? $missao->pontuacao) }}" min="0" max="{{ $missao->pontuacao }}" required
                                                                        class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">
                                                                </div>
                                                                <fieldset class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                    <legend class="text-xs font-semibold text-[--text-main] mb-2">Rubrica por competência</legend>
                                                                    @foreach (\App\Models\EquipeMissaoUser::COMPETENCIAS as $campo => $nome)
                                                                        <label>
                                                                            <span class="text-[10px] text-[--text-muted]">{{ $nome }}</span>
                                                                            <select name="{{ $campo }}" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-xs focus:border-excel-dark focus:ring-excel-light">
                                                                                <option value="">Selecione...</option>
                                                                                <option value="precisa_praticar" @selected(old($campo, $p->{$campo}) === 'precisa_praticar')>Precisa praticar</option>
                                                                                <option value="em_desenvolvimento" @selected(old($campo, $p->{$campo}) === 'em_desenvolvimento')>Em desenvolvimento</option>
                                                                                <option value="dominado" @selected(old($campo, $p->{$campo}) === 'dominado')>Dominado</option>
                                                                            </select>
                                                                        </label>
                                                                    @endforeach
                                                                </fieldset>
                                                                <label class="block">
                                                                    <span class="text-[10px] text-[--text-muted]">Feedback do professor (opcional)</span>
                                                                    <textarea name="feedback_professor" rows="3" maxlength="2000" class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">{{ old('feedback_professor', $p->feedback_professor) }}</textarea>
                                                                </label>
                                                                <label class="block">
                                                                    <span class="text-[10px] text-[--text-muted]">Próximo passo recomendado</span>
                                                                    <textarea name="proximo_passo" rows="2" maxlength="1000" class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">{{ old('proximo_passo', $p->proximo_passo) }}</textarea>
                                                                    <span class="text-[10px] text-[--text-muted]">Obrigatório quando alguma competência ainda não estiver dominada.</span>
                                                                </label>
                                                                <div class="flex justify-end gap-2">
                                                                    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-xs text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                                                    <x-button class="text-xs px-3 py-1.5">Salvar avaliação</x-button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                                @if ($missao->pivot->resposta || $missao->pivot->anexo_path)
                                                    <div class="rounded bg-white/70 px-2 py-1.5 text-[10px] text-[--text-main]">
                                                        @if ($missao->pivot->resposta)
                                                            <p class="whitespace-pre-line"><strong>Resposta da equipe:</strong> {{ $missao->pivot->resposta }}</p>
                                                        @endif
                                                        @if ($missao->pivot->anexo_path)
                                                            @if ($missao->pivot->anexo_removido_em || $equipe->turma?->concluida_em)
                                                                <span class="font-semibold text-amber-700">O arquivo não existe porque a turma foi concluída.</span>
                                                            @else
                                                                <a href="{{ route('missoes.anexo', $missao->pivot->id) }}" class="font-semibold text-excel-dark underline">Baixar anexo: {{ $missao->pivot->anexo_nome_original }}</a>
                                                            @endif
                                                        @endif
                                                        @if ($missao->pivot->feedback_reenvio)
                                                            <div class="mt-2 rounded border border-amber-200 bg-amber-50 px-2 py-1.5">
                                                                <strong>Feedback para reenvio:</strong>
                                                                <p class="whitespace-pre-line">{{ $missao->pivot->feedback_reenvio }}</p>
                                                                <span class="font-semibold {{ $missao->pivot->reenvio_entregue_em ? 'text-green-700' : 'text-amber-700' }}">
                                                                    {{ $missao->pivot->reenvio_entregue_em ? 'Novo anexo recebido' : 'Aguardando novo anexo da equipe' }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        @if ($missao->pivot->reenvio_entregue_em)
                                                            <p class="mt-2 font-semibold text-green-700">Novo anexo pronto para reavaliação. Use “Revisar avaliação” em cada integrante.</p>
                                                        @endif
                                                        @if ($missao->pivot->feedback_reformulacao)
                                                            <div class="mt-2 rounded border border-amber-200 bg-amber-50 px-2 py-1.5">
                                                                <strong>Feedback para reformulação:</strong>
                                                                <p class="whitespace-pre-line">{{ $missao->pivot->feedback_reformulacao }}</p>
                                                                <span class="font-semibold {{ $missao->pivot->reformulacao_entregue_em ? 'text-green-700' : 'text-amber-700' }}">
                                                                    {{ $missao->pivot->reformulacao_entregue_em ? 'Resposta reformulada recebida' : 'Aguardando reformulação da equipe' }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        @if ($missao->pivot->reformulacao_entregue_em)
                                                            <p class="mt-2 font-semibold text-green-700">Resposta reformulada pronta para reavaliação. Use “Revisar avaliação” em cada integrante.</p>
                                                        @endif
                                                    </div>
                                                    @if ($missao->permite_anexo && $missao->pivot->anexo_path && ! $equipe->turma?->concluida_em && ! ($missao->pivot->reenvio_solicitado_em && ! $missao->pivot->reenvio_entregue_em))
                                                        <button type="button" onclick="document.getElementById('reenvioModal{{ $missao->pivot->id }}').classList.remove('hidden')" class="mt-1 text-[10px] font-bold text-amber-700 underline hover:text-amber-900">
                                                            Solicitar reenvio do anexo
                                                        </button>
                                                        <div id="reenvioModal{{ $missao->pivot->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                                            <div class="bg-white rounded-excel w-full max-w-md shadow-xl overflow-hidden text-left">
                                                                <div class="bg-amber-600 px-5 py-3"><h3 class="text-white font-semibold">Solicitar reenvio — {{ $missao->titulo }}</h3></div>
                                                                <form method="POST" action="{{ route('missoes.solicitarReenvio', $missao->pivot->id) }}" class="p-5 space-y-4">
                                                                    @csrf
                                                                    <p class="text-xs text-[--text-muted]">A missão continuará concluída e nenhuma pontuação será criada ou alterada.</p>
                                                                    <div>
                                                                        <x-label for="feedbackReenvio{{ $missao->pivot->id }}" value="Feedback para a equipe" />
                                                                        <textarea id="feedbackReenvio{{ $missao->pivot->id }}" name="feedback_reenvio" rows="4" maxlength="2000" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-amber-600 focus:ring-amber-300" placeholder="Explique objetivamente o que deve ser corrigido no anexo."></textarea>
                                                                    </div>
                                                                    <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button><x-button>Solicitar reenvio</x-button></div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($missao->permite_resposta && ! $missao->permite_anexo && $missao->pivot->resposta && ! $equipe->turma?->concluida_em && ! ($missao->pivot->reformulacao_solicitada_em && ! $missao->pivot->reformulacao_entregue_em))
                                                        <button type="button" onclick="document.getElementById('reformulacaoModal{{ $missao->pivot->id }}').classList.remove('hidden')" class="mt-1 text-[10px] font-bold text-amber-700 underline hover:text-amber-900">
                                                            Solicitar reformulação da resposta
                                                        </button>
                                                        <div id="reformulacaoModal{{ $missao->pivot->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                                            <div class="bg-white rounded-excel w-full max-w-md shadow-xl overflow-hidden text-left">
                                                                <div class="bg-amber-600 px-5 py-3"><h3 class="text-white font-semibold">Solicitar reformulação — {{ $missao->titulo }}</h3></div>
                                                                <form method="POST" action="{{ route('missoes.solicitarReformulacao', $missao->pivot->id) }}" class="p-5 space-y-4">
                                                                    @csrf
                                                                    <p class="text-xs text-[--text-muted]">A missão continuará concluída e nenhuma pontuação será criada ou alterada automaticamente.</p>
                                                                    <div>
                                                                        <x-label for="feedbackReformulacao{{ $missao->pivot->id }}" value="Feedback para a equipe" />
                                                                        <textarea id="feedbackReformulacao{{ $missao->pivot->id }}" name="feedback_reformulacao" rows="4" maxlength="2000" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-amber-600 focus:ring-amber-300" placeholder="Explique objetivamente o que deve ser reformulado na resposta."></textarea>
                                                                    </div>
                                                                    <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button><x-button>Solicitar reformulação</x-button></div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                                @php
                                                    $concluidos = $progressos->where('status', 'concluida');
                                                    $todosConcluiram = $membrosPresentes->isNotEmpty() && $concluidos->count() === $membrosPresentes->count();
                                                    $tempoMedio = null;
                                                    if ($todosConcluiram) {
                                                        $segundos = (int) $concluidos->avg(fn($p) => $p->duracao_segundos);
                                                        if ($segundos > 0) {
                                                            $tempoMedio = sprintf('%d:%02d:%02d', floor($segundos / 3600), floor(($segundos % 3600) / 60), $segundos % 60);
                                                        }
                                                    }
                                                @endphp
                                                @if ($todosConcluiram && $tempoMedio)
                                                    <div class="text-[10px] text-[--text-muted] pt-1 border-t border-[--border-light] mt-1">
                                                        Tempo médio da equipe: <strong class="text-excel-dark">{{ $tempoMedio }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Ações --}}
                    <div class="border-t border-[--border-light] px-5 py-3 flex flex-wrap items-center gap-2">
                        @if ($user->isAluno() && $user->equipe_id === $equipe->id)
                            <div class="flex flex-col items-start gap-1">
                                <button onclick="document.getElementById('sairEquipeModal{{ $equipe->id }}').classList.remove('hidden')" class="text-xs text-red-500 hover:text-red-600 transition font-medium">Sair da Equipe</button>
                                @if ($equipe->missoes->isNotEmpty() && $equipe->alunos->where('id', '!=', $user->id)->isNotEmpty())
                                    <button onclick="document.getElementById('faltaGeralModal{{ $equipe->id }}').classList.remove('hidden')" class="text-xs text-red-500 hover:text-red-600 transition font-medium">Comunicar falta</button>
                                @endif
                            </div>

                            {{-- Modal Sair da Equipe --}}
                            <div id="sairEquipeModal{{ $equipe->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                <div class="bg-white rounded-excel w-full max-w-xs shadow-xl overflow-hidden">
                                    <div class="bg-red-600 px-4 py-3">
                                        <h3 class="text-white text-sm font-semibold">Sair da Equipe</h3>
                                    </div>
                                    <form method="POST" action="{{ route('equipes.sair') }}" class="p-4 space-y-3">
                                        @csrf
                                        <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                        <p class="text-xs text-[--text-muted]">Você sairá da equipe <strong>{{ $equipe->nome }}</strong>. É necessária a senha do professor.</p>
                                        <div>
                                            <input type="password" name="senha_professor" required placeholder="Senha do professor"
                                                class="block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light">
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-xs text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                            <x-danger-button class="text-xs px-3 py-1.5">Sair</x-danger-button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Modal geral para comunicar falta antes ou depois do início --}}
                            <div id="faltaGeralModal{{ $equipe->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden">
                                    <div class="bg-red-600 px-5 py-3"><h3 class="text-white font-semibold">Comunicar falta</h3></div>
                                    <form method="POST" action="{{ route('missoes.comunicarFalta') }}" class="p-5 space-y-4" onsubmit="return confirm('Confirmar esta falta? Esta ação não poderá ser desfeita.')">
                                        @csrf
                                        <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                        <div>
                                            <x-label for="faltaMissao{{ $equipe->id }}" value="Missão" />
                                            <select id="faltaMissao{{ $equipe->id }}" name="missao_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm bg-white focus:border-excel-dark focus:ring-excel-light">
                                                <option value="">Selecione a missão...</option>
                                                @foreach ($equipe->missoes as $missaoOpcao)<option value="{{ $missaoOpcao->id }}">{{ $missaoOpcao->titulo }}</option>@endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <x-label for="faltaMembro{{ $equipe->id }}" value="Integrante ausente" />
                                            <select id="faltaMembro{{ $equipe->id }}" name="user_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm bg-white focus:border-excel-dark focus:ring-excel-light">
                                                <option value="">Selecione o integrante...</option>
                                                @foreach ($equipe->alunos->where('id', '!=', $user->id) as $membro)<option value="{{ $membro->id }}">{{ $membro->name }}</option>@endforeach
                                            </select>
                                        </div>
                                        <p class="text-xs font-semibold text-red-600">Atenção: esta ação não poderá ser desfeita.</p>
                                        <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button><x-danger-button>Confirmar falta</x-danger-button></div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @can('update', $equipe)
                            <a href="{{ route('equipes.edit', $equipe) }}" class="text-xs text-[--text-muted] hover:text-excel-dark transition font-medium">
                                Editar
                            </a>
                        @endcan

                        @can('managePoints', $equipe)
                            <button onclick="document.getElementById('addPointsModal{{ $equipe->id }}').classList.remove('hidden')" class="text-xs text-excel-dark hover:text-excel-light transition font-medium">
                                + Pontos
                            </button>
                            <button onclick="document.getElementById('removePointsModal{{ $equipe->id }}').classList.remove('hidden')" class="text-xs text-red-500 hover:text-red-600 transition font-medium">
                                - Pontos
                            </button>
                        @endcan

                        @can('manageAlunos', $equipe)
                            <button onclick="document.getElementById('addAlunoModal{{ $equipe->id }}').classList.remove('hidden')" class="text-xs text-excel-dark hover:text-excel-light transition font-medium">
                                Adicionar aluno
                            </button>
                            @if ($equipe->alunos->isNotEmpty())
                                <button onclick="document.getElementById('removeAlunoModal{{ $equipe->id }}').classList.remove('hidden')" class="text-xs text-red-500 hover:text-red-600 transition font-medium">
                                    Remover aluno
                                </button>
                            @endif
                        @endcan

                        @can('delete', $equipe)
                            <form method="POST" action="{{ route('equipes.destroy', $equipe) }}" onsubmit="return confirm('Excluir equipe {{ $equipe->nome }}?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-600 transition font-medium">Excluir</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-[--text-muted]">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-3 text-[--excel-grid]">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <p>Nenhuma equipe encontrada.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $equipes->links() }}
        </div>

        {{-- Modals de Pontuação e Alunos --}}
        @foreach ($equipes as $equipe)
            @can('managePoints', $equipe)
                {{-- Add Points Modal --}}
                <div id="addPointsModal{{ $equipe->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
                    <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden">
                        <div class="excel-ribbon px-5 py-3">
                            <h3 class="text-white font-semibold">Adicionar Pontos — {{ $equipe->nome }}</h3>
                        </div>
                        <form method="POST" action="{{ route('equipes.addPoints', $equipe) }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <x-label for="points{{ $equipe->id }}" value="Quantidade de pontos" />
                                <x-input id="points{{ $equipe->id }}" type="number" name="points" min="1" max="1000" required class="block mt-1 w-full" />
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                <x-button>Adicionar</x-button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Remove Points Modal --}}
                <div id="removePointsModal{{ $equipe->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
                    <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden">
                        <div class="bg-red-600 px-5 py-3">
                            <h3 class="text-white font-semibold">Remover Pontos — {{ $equipe->nome }}</h3>
                        </div>
                        <form method="POST" action="{{ route('equipes.removePoints', $equipe) }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <x-label for="removePoints{{ $equipe->id }}" value="Quantidade de pontos" />
                                <x-input id="removePoints{{ $equipe->id }}" type="number" name="points" min="1" max="1000" required class="block mt-1 w-full" />
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                <x-danger-button>Remover</x-danger-button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            @can('manageAlunos', $equipe)
                @php
                    $alunosTurma = \App\Models\User::where('tipo', 'aluno')
                        ->whereHas('turmas', fn($q) => $q->where('turmas.id', $equipe->turma_id))
                        ->orderBy('name')->get();
                @endphp
                {{-- Add Aluno Modal --}}
                <div id="addAlunoModal{{ $equipe->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
                    <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden">
                        <div class="excel-ribbon px-5 py-3">
                            <h3 class="text-white font-semibold">Adicionar Aluno — {{ $equipe->nome }}</h3>
                        </div>
                        <form method="POST" action="{{ route('equipes.addAluno', $equipe) }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <x-label for="user_id{{ $equipe->id }}" value="Selecione o aluno" />
                                <select id="user_id{{ $equipe->id }}" name="user_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach ($alunosTurma as $aluno)
                                        <option value="{{ $aluno->id }}">
                                            {{ $aluno->name }} {{ $aluno->equipe_id ? '(já em equipe)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                <x-button>Adicionar</x-button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Remove Aluno Modal --}}
                @php $alunosEquipe = $equipe->alunos; @endphp
                @if ($alunosEquipe->isNotEmpty())
                <div id="removeAlunoModal{{ $equipe->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) this.classList.add('hidden')">
                    <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden">
                        <div class="bg-red-600 px-5 py-3">
                            <h3 class="text-white font-semibold">Remover Aluno — {{ $equipe->nome }}</h3>
                        </div>
                        <form method="POST" action="{{ route('equipes.removeAluno', $equipe) }}" class="p-5 space-y-4">
                            @csrf
                            <div>
                                <x-label for="rmUserId{{ $equipe->id }}" value="Selecione o aluno" />
                                <select id="rmUserId{{ $equipe->id }}" name="user_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach ($alunosEquipe as $aluno)
                                        <option value="{{ $aluno->id }}">{{ $aluno->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                <x-danger-button>Remover</x-danger-button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            @endcan
        @endforeach
    </div>

    {{-- Modal Criar Equipe pelo Aluno --}}
    @php $alunoUser = auth()->user(); @endphp
    @if ($alunoUser->isAluno() && !$alunoUser->equipe_id)
        @php $turmaAluno = $alunoUser->turmas()->with('alunos')->first(); @endphp
        <div id="criarEquipeAlunoModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
            <div class="bg-white rounded-excel w-full max-w-md shadow-xl overflow-hidden">
                <div class="excel-ribbon px-5 py-3">
                    <h3 class="text-white font-semibold">Criar Minha Equipe</h3>
                    <p class="text-xs text-white/60 mt-0.5">Selecione os colegas da sua turma</p>
                </div>
                <form method="POST" action="{{ route('equipes.criarPorAluno') }}" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <x-label for="nome_equipe_criar" value="Nome da Equipe" />
                        <input type="text" name="nome_equipe" id="nome_equipe_criar" required
                            class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light"
                            placeholder="Digite o nome da equipe">
                    </div>

                    @if ($turmaAluno)
                        <div>
                            <span class="text-xs text-[--text-muted] font-semibold uppercase tracking-wider block mb-2">
                                Alunos da turma {{ $turmaAluno->codigo }} (selecione os integrantes)
                            </span>
                            <div class="max-h-48 overflow-y-auto space-y-1 border border-[--border-light] rounded-excel p-2">
                                @foreach ($turmaAluno->alunos->where('equipe_id', null) as $colega)
                                    @if ($colega->id !== $alunoUser->id)
                                        <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-[#f8faf8] cursor-pointer">
                                            <input type="checkbox" name="aluno_ids[]" value="{{ $colega->id }}" class="rounded border-gray-300 text-excel-dark focus:ring-excel-light">
                                            <span class="text-sm text-[--text-main]">{{ $colega->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                                @if ($turmaAluno->alunos->where('equipe_id', null)->count() <= 1)
                                    <p class="text-xs text-[--text-muted] p-2">Nenhum colega sem equipe disponível.</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-red-500">Você não está vinculado a nenhuma turma.</p>
                    @endif

                    <div>
                        <x-label for="senha_professor_criar" value="Senha do Professor (autorização)" />
                        <input type="password" name="senha_professor" id="senha_professor_criar" required
                            class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm focus:border-excel-dark focus:ring-excel-light"
                            placeholder="Digite a senha do professor">
                        <p class="text-xs text-[--text-muted] mt-1">O professor precisa autorizar com a senha dele.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                        <x-button>Criar Equipe</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
