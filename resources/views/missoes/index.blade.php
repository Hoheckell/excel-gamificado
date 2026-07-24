<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Missões</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Desafios e pontuações do torneio</p>
            </div>
            @can('create', App\Models\Missao::class)
                <a href="{{ route('missoes.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nova Missão
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6">
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

        <div class="space-y-4">
            @forelse ($missoes as $missao)
                <div class="portal-container">
                    <div class="excel-ribbon px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-base">{{ $missao->ordem }}. {{ $missao->titulo }}</h3>
                                <div class="prose prose-sm prose-invert max-w-none max-h-12 overflow-hidden text-white/80 mt-1 [&_*]:my-0 [&_a]:text-white">
                                    {!! $missao->descricao_preview_html !!}
                                </div>
                                <div class="flex items-center gap-4 mt-1">
                                    <span class="text-white/70 text-xs">{{ $missao->created_at->format('d/m/Y') }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white">
                                        {{ $missao->pontuacao }} pts
                                    </span>
                                    <span class="text-white/70 text-xs">{{ $missao->equipes_count }} equipe(s)</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @can('update', $missao)
                                    <a href="{{ route('missoes.edit', $missao) }}" class="text-xs text-white/70 hover:text-white transition font-medium">Editar</a>
                                @endcan
                                @can('delete', $missao)
                                    <form method="POST" action="{{ route('missoes.destroy', $missao) }}" onsubmit="return confirm('Excluir esta missão?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-300 hover:text-red-100 transition font-medium">Excluir</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        @if ($missao->equipes->isNotEmpty())
                            <span class="text-[10px] text-[--text-muted] uppercase tracking-wider block mb-2">Equipes vinculadas</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($missao->equipes as $equipe)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-excel-tint text-excel-dark">
                                        {{ $equipe->nome }}
                                        @can('atribuirEquipes', $missao)
                                            <form method="POST" action="{{ route('missoes.removerEquipe', $missao) }}" class="inline" onsubmit="return confirm('Remover {{ $equipe->nome }} desta missão?')">
                                                @csrf
                                                <input type="hidden" name="equipe_id" value="{{ $equipe->id }}">
                                                <button class="ml-0.5 text-excel-dark/50 hover:text-red-600 transition">&times;</button>
                                            </form>
                                        @endcan
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-[--text-muted] italic">Nenhuma equipe vinculada.</p>
                        @endif

                        @can('atribuirEquipes', $missao)
                            <button onclick="document.getElementById('atribuirModal{{ $missao->id }}').classList.remove('hidden')"
                                class="mt-3 text-xs text-excel-dark hover:text-excel-light transition font-semibold">
                                + Atribuir Equipes
                            </button>

                            {{-- Modal atribuir equipes --}}
                            <div id="atribuirModal{{ $missao->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                <div class="bg-white rounded-excel w-full max-w-md shadow-xl overflow-hidden">
                                    <div class="excel-ribbon px-5 py-3">
                                        <h3 class="text-white font-semibold">Atribuir Equipes — {{ Str::limit(strip_tags($missao->descricao), 50) }}</h3>
                                    </div>
                                    <form method="POST" action="{{ route('missoes.atribuir', $missao) }}" class="p-5 space-y-4">
                                        @csrf
                                        <div>
                                            <span class="text-xs text-[--text-muted] font-semibold uppercase tracking-wider block mb-2">Selecione as equipes</span>
                                            <div class="max-h-56 overflow-y-auto space-y-1 border border-[--border-light] rounded-excel p-2">
                                                @php
                                                    $equipesDisponiveis = \App\Models\Equipe::orderBy('nome')->get();
                                                    $vinculadas = $missao->equipes->pluck('id')->toArray();
                                                @endphp
                                                @forelse ($equipesDisponiveis as $equipe)
                                                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-[#f8faf8] cursor-pointer">
                                                        <input type="checkbox" name="equipe_ids[]" value="{{ $equipe->id }}" {{ in_array($equipe->id, $vinculadas) ? 'checked' : '' }} class="rounded border-gray-300 text-excel-dark focus:ring-excel-light">
                                                        <span class="text-sm text-[--text-main]">{{ $equipe->nome }}</span>
                                                    </label>
                                                @empty
                                                    <p class="text-xs text-[--text-muted] p-2">Nenhuma equipe cadastrada.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="flex justify-end gap-3">
                                            <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</button>
                                            <x-button>Salvar</x-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endcan

                        @php
                            $alunoLogado = auth()->user();
                            $equipeDoAluno = $alunoLogado->isAluno() && $alunoLogado->equipe_id
                                ? $missao->equipes->firstWhere('id', $alunoLogado->equipe_id)
                                : null;
                            $progressosEquipe = $equipeDoAluno
                                ? $missao->progresso->where('equipe_id', $equipeDoAluno->id)
                                : collect();
                            $missaoIniciada = $progressosEquipe->contains(fn ($p) => in_array($p->status, ['em_andamento', 'concluida']));
                            $idsIndisponiveis = $progressosEquipe->whereIn('status', ['ausente', 'concluida'])->pluck('user_id');
                            $candidatosFalta = $equipeDoAluno
                                ? $equipeDoAluno->alunos->where('id', '!=', $alunoLogado->id)->whereNotIn('id', $idsIndisponiveis)
                                : collect();
                        @endphp
                        @if ($missaoIniciada && $candidatosFalta->isNotEmpty())
                            <button type="button" onclick="document.getElementById('faltaNaMissaoModal{{ $missao->id }}').classList.remove('hidden')" class="mt-3 block text-xs font-semibold text-red-600 hover:text-red-700 transition">Comunicar falta</button>
                            <div id="faltaNaMissaoModal{{ $missao->id }}" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
                                <div class="bg-white rounded-excel w-full max-w-sm shadow-xl overflow-hidden text-left">
                                    <div class="bg-red-600 px-5 py-3"><h3 class="text-white font-semibold">Comunicar falta</h3></div>
                                    <form method="POST" action="{{ route('missoes.comunicarFalta') }}" class="p-5 space-y-4" onsubmit="return confirm('Confirmar esta falta? Esta ação não poderá ser desfeita.')">
                                        @csrf
                                        <input type="hidden" name="equipe_id" value="{{ $equipeDoAluno->id }}">
                                        <input type="hidden" name="missao_id" value="{{ $missao->id }}">
                                        <p class="text-xs text-[--text-muted]">Selecione quem não está presente na missão <strong>{{ $missao->titulo }}</strong>.</p>
                                        <select name="user_id" required class="block w-full border border-[--border-light] rounded-excel px-3 py-2 text-sm bg-white focus:border-excel-dark focus:ring-excel-light">
                                            <option value="">Selecione o integrante ausente...</option>
                                            @foreach ($candidatosFalta as $membro)<option value="{{ $membro->id }}">{{ $membro->name }}</option>@endforeach
                                        </select>
                                        <p class="text-xs font-semibold text-red-600">Atenção: esta ação não poderá ser desfeita.</p>
                                        <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="text-sm text-[--text-muted]">Cancelar</button><x-danger-button>Confirmar falta</x-danger-button></div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-[--text-muted]">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-3 text-[--excel-grid]">
                        <path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6h-5.6z"/>
                    </svg>
                    <p>Nenhuma missão cadastrada.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $missoes->links() }}
        </div>
    </div>
</x-app-layout>
