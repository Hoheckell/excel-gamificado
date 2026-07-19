<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">{{ $equipe->nome }}</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto p-6">
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-5 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-[48px] h-[48px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($equipe->nome, 0, 2)) }}
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-[--text-main]">{{ $equipe->nome }}</h1>
                        <p class="text-sm text-[--text-muted]">Turma: {{ $equipe->turma->codigo ?? '—' }} — {{ $equipe->turma->descricao ?? '' }}</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-excel-dark">{{ $equipe->pontuacao }} pts</span>
            </div>

            <div class="p-8">
                <h3 class="font-semibold text-[--text-main] mb-4">Alunos ({{ $equipe->alunos->count() }})</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse ($equipe->alunos as $aluno)
                        <div class="flex items-center gap-3 p-3 rounded-excel bg-[#f8faf8]">
                            <div class="w-[34px] h-[34px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($aluno->name, 0, 2)) }}
                            </div>
                            <span class="text-sm font-medium text-[--text-main]">{{ $aluno->name }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-[--text-muted] italic col-span-full">Nenhum aluno na equipe.</p>
                    @endforelse
                </div>

                <div class="flex justify-end mt-6 gap-3">
                    <a href="{{ route('equipes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Voltar</a>
                    @can('update', $equipe)
                        <a href="{{ route('equipes.edit', $equipe) }}" class="btn-excel inline-block text-sm px-4 py-2">Editar</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
