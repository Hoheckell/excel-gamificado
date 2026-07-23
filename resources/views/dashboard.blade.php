<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ auth()->user()->isAluno() ? 'Minha Jornada' : 'Painel do Sistema Pedagógico' }}
            </h2>
            @if (auth()->user()->isAluno())
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Aprender, praticar, receber feedback e evoluir</p>
            @endif
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto p-6">
        @if (! auth()->user()->isAluno())
            @if ($equipesParaReagrupar->isNotEmpty())
                <section class="mb-5 rounded-excel border border-blue-200 bg-blue-50 p-5">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">💡</span>
                        <div class="flex-1">
                            <h3 class="font-semibold text-blue-900">Sugestão pedagógica de reagrupamento</h3>
                            <p class="text-xs text-blue-800 mt-1">Após a terceira missão, estas equipes possuem menos de três membros autorizados. A Multiclasse mantém a aula funcionando; qualquer fusão continua sendo uma decisão manual do professor.</p>
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach ($equipesParaReagrupar as $equipeReduzida)
                                    <span class="inline-flex rounded-full border border-blue-200 bg-white px-3 py-1 text-xs text-blue-900">
                                        {{ $equipeReduzida->nome }} · {{ $equipeReduzida->alunos_ativos_count }} ativo(s) · {{ $equipeReduzida->turma?->codigo }}
                                    </span>
                                @endforeach
                            </div>
                            <a href="{{ route('equipes.index') }}" class="inline-flex mt-4 text-xs font-semibold text-blue-900 underline">Abrir gerenciamento manual de equipes</a>
                        </div>
                    </div>
                </section>
            @endif
            <div class="portal-container">
                <x-welcome />
            </div>
        @elseif (! $jornada)
            <div class="portal-container p-10 text-center">
                <div class="text-4xl mb-3">🧭</div>
                <h3 class="font-semibold text-lg text-[--text-main]">Sua jornada está quase pronta</h3>
                <p class="text-sm text-[--text-muted] mt-2">Entre em uma equipe para receber missões, assumir papéis e acompanhar sua evolução.</p>
                <a href="{{ route('equipes.index') }}" class="inline-flex mt-5 px-4 py-2 bg-excel-dark text-white rounded-excel text-sm font-semibold">Encontrar minha equipe</a>
            </div>
        @else
            @php
                $equipe = $jornada['equipe'];
                $missaoAtual = $jornada['missaoAtual'];
                $progressoAtual = $jornada['progressoAtual'];
                $ultimoFeedback = $jornada['ultimoFeedback'];
                $totalMissoes = $jornada['missoes']->count();
                $percentualMissoes = $totalMissoes ? round(($jornada['concluidas'] / $totalMissoes) * 100) : 0;
                $rotulos = [
                    'precisa_praticar' => ['Precisa praticar', 'bg-amber-50 text-amber-800 border-amber-200'],
                    'em_desenvolvimento' => ['Em desenvolvimento', 'bg-blue-50 text-blue-800 border-blue-200'],
                    'dominado' => ['Dominado', 'bg-green-50 text-green-800 border-green-200'],
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <section class="portal-container lg:col-span-2 overflow-hidden">
                    <div class="excel-ribbon px-6 py-4">
                        <p class="text-xs uppercase tracking-widest text-white/70">Agora</p>
                        <h3 class="text-white text-lg font-semibold mt-1">{{ $missaoAtual?->titulo ?? 'Todas as missões concluídas' }}</h3>
                    </div>
                    <div class="p-6">
                        @if ($missaoAtual)
                            <p class="text-sm text-[--text-muted]">{{ $missaoAtual->descricao }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5">
                                <div class="rounded-excel bg-[#f8faf8] border border-[--border-light] p-4">
                                    <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Seu papel</span>
                                    <strong class="block text-sm text-[--text-main] mt-1">{{ $progressoAtual?->papeis_nomes ? implode(' + ', $progressoAtual->papeis_nomes) : 'A definir com a equipe' }}</strong>
                                    @if (($missaoAtual?->pivot->tempo_extra_minutos ?? 0) > 0)
                                        <span class="inline-flex mt-2 text-[10px] font-semibold text-blue-700">+{{ $missaoAtual->pivot->tempo_extra_minutos }} minutos · Contrato Enxuto</span>
                                    @endif
                                </div>
                                <div class="rounded-excel bg-[#f8faf8] border border-[--border-light] p-4">
                                    <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Estado</span>
                                    <strong class="block text-sm text-[--text-main] mt-1">
                                        {{ match ($progressoAtual?->status) {
                                            'em_andamento' => 'Prática em andamento',
                                            'concluida' => 'Sua participação foi concluída',
                                            'ausente' => 'Ausência registrada sem penalidade',
                                            default => 'Pronta para iniciar',
                                        } }}
                                    </strong>
                                </div>
                            </div>
                            <a href="{{ route('missoes.index') }}" class="inline-flex mt-5 px-4 py-2 bg-excel-dark text-white rounded-excel text-sm font-semibold">Abrir missão</a>
                        @else
                            <p class="text-sm text-[--text-muted]">Você chegou a um ponto natural de conclusão. Revise suas conquistas de aprendizagem e reconheça o caminho percorrido.</p>
                        @endif
                    </div>
                </section>

                <aside class="portal-container p-6">
                    <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Próxima ação</span>
                    <div class="text-3xl mt-3">🎯</div>
                    <p class="text-sm font-semibold text-[--text-main] mt-3">{{ $jornada['proximaAcao'] }}</p>
                    <p class="text-xs text-[--text-muted] mt-3">Sem contagem diária e sem punição por pausa: avance no ritmo da aula e da sua equipe.</p>
                </aside>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-5">
                <section class="portal-container p-6">
                    <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Progresso nas missões</span>
                    <div class="flex items-end justify-between mt-2">
                        <strong class="text-3xl text-excel-dark">{{ $jornada['concluidas'] }}<span class="text-base text-[--text-muted]">/{{ $totalMissoes }}</span></strong>
                        <span class="text-xs font-semibold text-[--text-muted]">{{ $percentualMissoes }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden mt-3" role="progressbar" aria-label="Missões concluídas" aria-valuenow="{{ $percentualMissoes }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full bg-excel-light rounded-full" style="width: {{ $percentualMissoes }}%"></div>
                    </div>
                    <p class="text-xs text-[--text-muted] mt-3">A barra representa missões concluídas, não uma comparação com colegas.</p>
                </section>

                <section class="portal-container p-6">
                    <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Patamar da equipe</span>
                    <strong class="block text-xl text-[--text-main] mt-2">{{ $jornada['categoria']?->nome ?? 'Crescimento' }}</strong>
                    <p class="text-sm text-[--text-muted] mt-1">{{ $equipe->nome }} · {{ $equipe->xp_total }} de 500 XP</p>
                    <a href="{{ route('placar.index') }}" class="inline-flex mt-4 text-xs font-semibold text-excel-dark">Ver progresso da equipe →</a>
                </section>

                <section class="portal-container p-6">
                    <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Evidências conquistadas</span>
                    <strong class="block text-xl text-[--text-main] mt-2">{{ $equipe->badges->count() }}</strong>
                    <p class="text-xs text-[--text-muted] mt-1">Badges registram competências e atitudes já demonstradas; nenhuma é obrigatória para avançar.</p>
                </section>
            </div>

            <section class="portal-container mt-5 overflow-hidden">
                <div class="px-6 py-4 border-b border-[--border-light]">
                    <h3 class="font-semibold text-[--text-main]">Meu feedback mais recente</h3>
                    <p class="text-xs text-[--text-muted] mt-1">Somente você e seus professores podem consultar esta devolutiva individual.</p>
                </div>
                <div class="p-6">
                    @if ($ultimoFeedback)
                        <div class="flex flex-wrap gap-2">
                            @foreach (\App\Models\EquipeMissaoUser::COMPETENCIAS as $campo => $nome)
                                @if ($ultimoFeedback->{$campo})
                                    @php([$rotulo, $classe] = $rotulos[$ultimoFeedback->{$campo}])
                                    <span class="inline-flex flex-col border rounded-excel px-3 py-2 {{ $classe }}">
                                        <span class="text-[10px] opacity-75">{{ $nome }}</span>
                                        <strong class="text-xs">{{ $rotulo }}</strong>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        @if ($ultimoFeedback->feedback_professor)
                            <div class="mt-5">
                                <span class="text-[10px] uppercase tracking-wider text-[--text-muted]">Devolutiva</span>
                                <p class="text-sm text-[--text-main] mt-1">{{ $ultimoFeedback->feedback_professor }}</p>
                            </div>
                        @endif
                        @if ($ultimoFeedback->proximo_passo)
                            <div class="mt-4 rounded-excel bg-excel-tint border border-excel-light p-4">
                                <span class="text-[10px] uppercase tracking-wider text-excel-dark">Próximo passo recomendado</span>
                                <p class="text-sm font-semibold text-excel-dark mt-1">{{ $ultimoFeedback->proximo_passo }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-[--text-muted]">Seu feedback aparecerá aqui depois que o professor avaliar uma missão concluída.</p>
                    @endif
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
