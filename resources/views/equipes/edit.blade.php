<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Editar Equipe: {{ $equipe->nome }}</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-6">
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-[42px] h-[42px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($equipe->nome, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-[--text-main]">{{ $equipe->nome }}</p>
                        <p class="text-xs text-[--text-muted]">{{ $equipe->pontuacao }} pontos · Turma: {{ $equipe->turma->codigo ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('equipes.update', $equipe) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <x-label for="turma_id" value="Turma" />
                            <select id="turma_id" name="turma_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id', $equipe->turma_id) == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->codigo }} - {{ $turma->descricao }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="turma_id" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="nome" value="Nome da Equipe" />
                            <x-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $equipe->nome)" required />
                            <x-input-error for="nome" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('equipes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Salvar</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
