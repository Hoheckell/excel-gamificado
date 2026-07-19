<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Alunos') }}
                </h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Listagem de Alunos</p>
            </div>
            @can('create', App\Models\User::class)
                <a href="{{ route('alunos.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Novo Aluno
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

        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                @if (auth()->user()->isProfessor() && isset($turmas))
                    <form method="GET" class="flex flex-wrap items-center gap-3">
                        <select name="turma_id" onchange="this.form.submit()" class="border border-[--border-light] rounded-excel px-3 py-2 text-sm text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light">
                            <option value="">Todas as Turmas</option>
                            @foreach ($turmas as $turma)
                                <option value="{{ $turma->id }}" {{ request('turma_id') == $turma->id ? 'selected' : '' }}>
                                    {{ $turma->codigo }} - {{ $turma->descricao }}
                                    @if ($turma->dt_fim && $turma->dt_fim->isPast())
                                        (encerrada)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <label class="flex items-center gap-1.5 text-xs text-[--text-muted] cursor-pointer">
                            <input type="checkbox" name="inativas" value="1" onchange="this.form.submit()" {{ $mostrarInativas ? 'checked' : '' }} class="rounded border-gray-300 text-excel-dark focus:ring-excel-light">
                            Mostrar turmas encerradas
                        </label>
                    </form>
                @endif
                <span class="text-sm text-[--text-muted]">{{ $alunos instanceof \Illuminate\Pagination\LengthAwarePaginator ? $alunos->total() : count($alunos) }} alunos encontrados</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#f8faf8] border-b border-[--border-light]">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Nome</th>
                            <th class="text-left px-6 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">E-mail</th>
                            <th class="text-left px-6 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Equipe</th>
                            @if (auth()->user()->isProfessor())
                                <th class="text-left px-6 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Turma</th>
                                <th class="text-center px-4 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Autorizado</th>
                            @endif
                            <th class="text-left px-6 py-3 font-semibold text-[--text-muted] text-xs uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[--border-light]">
                        @forelse ($alunos as $aluno)
                            <tr class="hover:bg-excel-tint/50 transition-colors">
                                <td class="px-6 py-3 font-medium text-[--text-main]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-[34px] h-[34px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($aluno->name, 0, 2)) }}
                                        </div>
                                        {{ $aluno->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-[--text-muted]">
                                    {{ auth()->user()->isProfessor() ? $aluno->email : '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    @if ($aluno->equipe)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-excel-tint text-excel-dark">
                                            {{ $aluno->equipe->nome }}
                                        </span>
                                    @else
                                        <span class="text-[--text-muted]">—</span>
                                    @endif
                                </td>
                                @if (auth()->user()->isProfessor())
                                    <td class="px-6 py-3 text-[--text-muted]">
                                        @foreach ($aluno->turmas as $turma)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 mr-1">
                                                {{ $turma->codigo }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form method="POST" action="{{ route('alunos.autorizar', $aluno) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold px-2 py-1 rounded {{ $aluno->autorizado ? 'bg-excel-tint text-excel-dark' : 'bg-gray-100 text-gray-400' }} hover:opacity-80 transition" title="{{ $aluno->autorizado ? 'Desautorizar' : 'Autorizar' }}">
                                                {{ $aluno->autorizado ? 'Sim' : 'Não' }}
                                            </button>
                                        </form>
                                    </td>
                                @endif
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        @if (auth()->user()->isProfessor() && $aluno->certificados->isNotEmpty())
                                            <form method="POST" action="{{ route('alunos.reenviarCertificado', $aluno) }}" onsubmit="return confirm('Reenviar o certificado para {{ $aluno->email }}?')" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 rounded-excel text-[--text-muted] hover:text-excel-dark hover:bg-excel-tint transition" title="Reenviar Certificado">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        @can('update', $aluno)
                                            <a href="{{ route('alunos.edit', $aluno) }}" class="p-1.5 rounded-excel text-[--text-muted] hover:text-excel-dark hover:bg-excel-tint transition" title="Editar">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                            </a>
                                        @endcan
                                        @can('delete', $aluno)
                                            <form method="POST" action="{{ route('alunos.destroy', $aluno) }}" onsubmit="return confirm('Remover {{ $aluno->name }}?')" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-excel text-[--text-muted] hover:text-red-600 hover:bg-red-50 transition" title="Excluir">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isProfessor() ? 5 : 4 }}" class="px-6 py-12 text-center text-[--text-muted]">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor" class="text-[--excel-grid]">
                                            <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5-4-8-4z"/>
                                        </svg>
                                        <span>Nenhum aluno encontrado.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($alunos instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="px-6 py-4 border-t border-[--border-light]">
                    {{ $alunos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
