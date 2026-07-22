<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">{{ $turma->descricao }}</h2>
                <p class="text-xs text-white/70 mt-0.5">Turma {{ $turma->codigo }}</p>
            </div>
            @can('update', $turma)
                <a href="{{ route('turmas.edit', $turma) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    Editar
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6">
        <div class="portal-container mb-6">
            <div class="bg-white border-b border-[--border-light] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-[52px] h-[52px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-lg font-mono">
                        {{ strtoupper(substr($turma->codigo, 0, 3)) }}
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-[--text-main]">{{ $turma->descricao }}</h1>
                        <p class="text-sm text-[--text-muted]">
                            <span class="font-mono font-bold text-excel-dark">{{ $turma->codigo }}</span>
                            &nbsp;·&nbsp;
                            {{ $turma->dt_inicio?->format('d/m/Y') ?? '—' }} a {{ $turma->dt_fim?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-2xl font-bold text-excel-dark block">{{ $turma->alunos_count }}</span>
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">Alunos</span>
                    </div>
                    <div class="text-center p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-2xl font-bold text-excel-dark block">{{ $turma->equipes_count }}</span>
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">Equipes</span>
                    </div>
                    <div class="text-center p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-2xl font-bold text-excel-dark block">{{ $turma->users->count() ?? 0 }}</span>
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">Membros</span>
                    </div>
                </div>

                @if ($turma->equipes->isNotEmpty())
                    <h3 class="font-semibold text-sm text-[--text-main] mb-3">Equipes ({{ $turma->equipes_count }})</h3>
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach ($turma->equipes as $equipe)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-excel-tint text-excel-dark">
                                {{ $equipe->nome }}
                                <span class="text-[10px] opacity-60">{{ $equipe->pontuacao }}pts</span>
                            </span>
                        @endforeach
                    </div>
                @endif

                <h3 class="font-semibold text-sm text-[--text-main] mb-3">Usuários ({{ $turma->users->count() }})</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($turma->users as $user)
                        <div class="flex items-center gap-2 p-2 rounded-excel bg-[#f8faf8]">
                            <div class="w-7 h-7 rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-[10px]">
                                {{ Str::substr($user->name, 0, 2) }}
                            </div>
                            <span class="text-xs font-medium">{{ $user->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $user->tipo === 'professor' ? 'bg-excel-dark text-white' : 'bg-gray-200 text-gray-600' }}">
                                {{ $user->tipo === 'professor' ? 'Prof' : 'Aluno' }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('turmas.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
