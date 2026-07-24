<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Detalhes do Aluno') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-6">
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-5">
                <div class="flex items-center gap-4">
                    <div class="w-[48px] h-[48px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($aluno->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-[--text-main]">{{ $aluno->name }}</h1>
                        <p class="text-sm text-[--text-muted]">
                            @foreach ($aluno->turmas as $turma)
                                <span class="mr-1">{{ $turma->codigo }}</span>
                            @endforeach
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">E-mail</span>
                        <p class="font-semibold text-[--text-main] mt-0.5">{{ $aluno->email }}</p>
                        <p class="mt-1 text-xs font-semibold {{ $aluno->educational_emails_consent ? 'text-green-700' : 'text-gray-500' }}">
                            Conteúdo educacional opcional: {{ $aluno->educational_emails_consent ? 'autorizado' : 'não autorizado' }}
                        </p>
                    </div>
                    <div class="p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">Equipe</span>
                        <p class="font-semibold text-[--text-main] mt-0.5">
                            {{ $aluno->equipe?->nome ?? 'Sem equipe' }}
                        </p>
                    </div>
                    <div class="p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">Tipo</span>
                        <p class="font-semibold text-[--text-main] mt-0.5">{{ ucfirst($aluno->tipo) }}</p>
                    </div>
                    <div class="p-4 rounded-excel bg-[#f8faf8]">
                        <span class="text-xs text-[--text-muted] uppercase tracking-wider">Autorizado</span>
                        <p class="font-semibold mt-0.5 {{ $aluno->autorizado ? 'text-excel-dark' : 'text-red-500' }}">
                            {{ $aluno->autorizado ? 'Sim' : 'Não' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('alunos.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">
                        Voltar
                    </a>
                    @can('update', $aluno)
                        <a href="{{ route('alunos.edit', $aluno) }}" class="btn-excel inline-block text-sm px-4 py-2">
                            Editar
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
