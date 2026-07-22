<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Turmas</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Gerenciamento de Turmas</p>
            </div>
            @can('create', App\Models\Turma::class)
                <a href="{{ route('turmas.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nova Turma
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-6 px-5 py-3 bg-excel-tint border border-excel-light text-excel-dark rounded-excel text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="portal-container mb-6">
            <div class="bg-white border-b border-[--border-light] px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por código ou descrição..." class="border border-[--border-light] rounded-excel px-3 py-2 text-sm text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light w-64">
                    <button type="submit" class="btn-excel text-xs px-4 py-2">Filtrar</button>
                </form>
                <span class="text-sm text-[--text-muted]">{{ $turmas->total() }} turmas</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse ($turmas as $turma)
                <div class="portal-container hover:shadow-excel transition-all duration-200">
                    <div class="excel-ribbon px-5 py-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs text-white/60 uppercase tracking-widest font-mono">{{ $turma->codigo }}</span>
                                <h3 class="text-white font-semibold text-base mt-0.5">{{ $turma->descricao }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center p-3 rounded-excel bg-[#f8faf8]">
                                <span class="text-lg font-bold text-excel-dark block">{{ $turma->alunos_count }}</span>
                                <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Alunos</span>
                            </div>
                            <div class="text-center p-3 rounded-excel bg-[#f8faf8]">
                                <span class="text-lg font-bold text-excel-dark block">{{ $turma->equipes_count }}</span>
                                <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Equipes</span>
                            </div>
                            <div class="text-center p-3 rounded-excel bg-[#f8faf8]">
                                <span class="text-lg font-bold text-excel-dark block">{{ $turma->professores_count ?? 0 }}</span>
                                <span class="text-[10px] text-[--text-muted] uppercase tracking-wider">Prof.</span>
                            </div>
                        </div>

                        <div class="space-y-1.5 mb-4">
                            <div class="flex justify-between text-xs">
                                <span class="text-[--text-muted]">Início</span>
                                <span class="font-medium text-[--text-main]">{{ $turma->dt_inicio?->format('d/m/Y') ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-[--text-muted]">Término</span>
                                <span class="font-medium text-[--text-main]">{{ $turma->dt_fim?->format('d/m/Y') ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-[--border-light]">
                            <a href="{{ route('turmas.show', $turma) }}" class="text-xs text-[--text-muted] hover:text-excel-dark transition font-medium">Ver</a>
                            @can('update', $turma)
                                <a href="{{ route('turmas.edit', $turma) }}" class="text-xs text-[--text-muted] hover:text-excel-dark transition font-medium">Editar</a>
                            @endcan
                            @can('delete', $turma)
                                <form method="POST" action="{{ route('turmas.destroy', $turma) }}" onsubmit="return confirm('Excluir turma {{ $turma->codigo }}?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-600 transition font-medium">Excluir</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-[--text-muted]">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-3 text-[--excel-grid]">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/>
                    </svg>
                    <p>Nenhuma turma encontrada.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $turmas->links() }}
        </div>
    </div>
</x-app-layout>
