<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Nova Equipe</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-6">
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-4">
                <p class="text-sm text-[--text-muted]">Preencha os dados para criar uma nova equipe.</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('equipes.store') }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-label for="turma_id" value="Turma" />
                            <select id="turma_id" name="turma_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                <option value="">Selecione a turma</option>
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->codigo }} - {{ $turma->descricao }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="turma_id" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="nome" value="Nome da Equipe" />
                            <x-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus />
                            <x-input-error for="nome" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="pontuacao" value="Pontuação Inicial" />
                            <x-input id="pontuacao" class="block mt-1 w-full" type="number" name="pontuacao" :value="old('pontuacao', 0)" min="0" />
                            <x-input-error for="pontuacao" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('equipes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Criar Equipe</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
