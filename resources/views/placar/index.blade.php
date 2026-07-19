<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Placar Geral</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Desempenho das equipes</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6">
        @if ($turmas->isEmpty())
            <div class="portal-container text-center p-12">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-4 text-[--excel-grid]">
                    <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.992 5.992 0 0 0 11 15.9V19H7v2h10v-2h-4v-3.1c2.45-.39 4.34-2.31 4.76-4.79C20.14 10.74 21 8.97 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.39 5 9.3 5 8zm14 0c0 1.3-.84 2.39-2 2.82V7h2v1z"/>
                </svg>
                <p class="text-[--text-muted]">Nenhuma turma encontrada.</p>
            </div>
        @else
            <div class="mb-6">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <span class="text-xs text-[--text-muted] font-semibold uppercase tracking-wider">Turma:</span>
                    <select name="turma_id" onchange="this.form.submit()" class="border border-[--border-light] rounded-excel px-3 py-2 text-sm text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light">
                        @foreach ($turmas as $turma)
                            <option value="{{ $turma->id }}" {{ $turmaSelecionada && $turmaSelecionada->id == $turma->id ? 'selected' : '' }}>
                                {{ $turma->codigo }} — {{ $turma->descricao }}
                                @if ($turma->dt_fim && $turma->dt_fim->isPast()) (encerrada) @endif
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if ($equipes->isEmpty())
                <div class="text-center py-12 text-[--text-muted]">
                    <p>Nenhuma equipe nesta turma.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($equipes as $posicao => $equipe)
                        @php
                            $corCategoria = $equipe->categoria_atual?->cor ?? '#107c41';
                            $pontos = $equipe->pontuacao;
                            $larguraBarra = min(100, ($pontos / 500) * 100);
                        @endphp
                        <div class="portal-container">
                            <div class="flex items-stretch">
                                {{-- Posição --}}
                                <div class="flex items-center justify-center w-14 shrink-0 rounded-tl-lg rounded-bl-lg" style="background-color: {{ $corCategoria }};">
                                    <span class="text-white font-bold text-lg">
                                        @if ($posicao === 0) &#9733;
                                        @elseif ($posicao === 1) &#9734;
                                        @elseif ($posicao === 2) &#9733;
                                        @else {{ $posicao + 1 }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Conteúdo --}}
                                <div class="flex-1 min-w-0">
                                    <div class="px-5 py-4 border-b border-[--border-light]">
                                        <div class="flex items-center justify-between gap-4">
                                            <div>
                                                <h3 class="font-bold text-base text-[--text-main]">{{ $equipe->nome }}</h3>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    @if ($equipe->categoria_atual)
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold text-white" style="background-color: {{ $corCategoria }};">
                                                            {{ $equipe->categoria_atual->nome }}
                                                        </span>
                                                    @endif
                                                    <span class="text-xs text-[--text-muted]">{{ $equipe->alunos_count }} membros</span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-2xl font-bold" style="color: {{ $corCategoria }};">{{ $pontos }}</span>
                                                <span class="text-xs text-[--text-muted] block">pontos</span>
                                            </div>
                                        </div>
                                        {{-- Barra de progresso --}}
                                        <div class="mt-2 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                            <div class="h-full rounded-full transition-all" style="width: {{ $larguraBarra }}%; background-color: {{ $corCategoria }};"></div>
                                        </div>
                                    </div>

                                    {{-- Expansão: Membros e Missões --}}
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open"
                                            class="w-full px-5 py-2 flex items-center justify-between text-xs font-semibold text-[--text-muted] hover:bg-[#f8faf8] hover:text-excel-dark transition">
                                            <span>Ver membros e missões</span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="transition-transform" :class="open ? 'rotate-180' : ''">
                                                <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-cloak class="border-t border-[--border-light] px-5 py-3 space-y-4">
                                            @foreach ($equipe->missoes_com_pontuacao as $missao)
                                                <div class="rounded-excel bg-[#f8faf8] p-3">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <p class="text-xs text-[--text-main] font-medium flex-1 pr-3">{{ Str::limit($missao->descricao, 120) }}</p>
                                                        <span class="text-[10px] font-bold text-excel-dark bg-excel-tint px-2 py-0.5 rounded-full shrink-0">{{ $missao->pontuacao }}pts</span>
                                                    </div>
                                                    <div class="space-y-1">
                                                        @foreach ($missao->progresso as $p)
                                                            <div class="flex items-center justify-between text-[10px]">
                                                                <span class="text-[--text-muted] flex items-center gap-1.5">
                                                                    <span class="w-4 h-4 rounded-full bg-excel-tint text-excel-dark flex items-center justify-center text-[7px] font-bold">
                                                                        {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                                                                    </span>
                                                                    {{ $p->user->name ?? '—' }}
                                                                </span>
                                                                <span>
                                                                    @if ($p->status === 'concluida')
                                                                        <span class="text-green-700 font-semibold">{{ $p->duracao }}</span>
                                                                        @if ($p->pontuacao_obtida !== null)
                                                                            <span class="text-excel-dark font-bold ml-1.5">{{ $p->pontuacao_obtida }}pts</span>
                                                                        @endif
                                                                    @elseif ($p->status === 'em_andamento')
                                                                        <span class="text-orange-500">Em andamento</span>
                                                                    @else
                                                                        <span class="text-[--text-muted]">Pendente</span>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                        @if ($missao->progresso->isEmpty())
                                                            <p class="text-[10px] text-[--text-muted] italic">Nenhum progresso registrado.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($equipe->missoes_com_pontuacao->isEmpty())
                                                <p class="text-xs text-[--text-muted] italic">Nenhuma missão atribuída.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
