<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Sorteio de Equipes</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Configurar Sorteio</h3>
            </div>

            <div class="p-8">
                <p class="text-sm text-[--text-muted] mb-6">Escolha a turma e a quantidade de alunos por equipe. Os alunos serão distribuídos aleatoriamente.</p>

                <form method="POST" action="{{ route('sorteio.sortear') }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-label for="turma_id" value="Turma" />
                            <select id="turma_id" name="turma_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                <option value="">Selecione a turma</option>
                                @foreach ($turmas as $turma)
                                    <option value="{{ $turma->id }}" {{ old('turma_id') == $turma->id ? 'selected' : '' }}>
                                        {{ $turma->codigo }} — {{ $turma->descricao }}
                                        ({{ $turma->alunos()->count() }} alunos)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="turma_id" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="integrantes" value="Alunos por Equipe" />
                            <x-input id="integrantes" class="block mt-1 w-full" type="number" name="integrantes" :value="old('integrantes', 4)" min="1" max="20" required />
                            <x-input-error for="integrantes" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('equipes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Sortear Equipes</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
