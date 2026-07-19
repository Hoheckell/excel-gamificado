<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Editar Turma: {{ $turma->codigo }}</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4 flex items-center gap-3">
                <span class="bg-white/20 text-white px-2.5 py-1 rounded text-xs font-mono font-bold tracking-wider">{{ $turma->codigo }}</span>
                <span class="text-white/60 text-xs">Código imutável</span>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('turmas.update', $turma) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <x-label for="descricao" value="Descrição da Turma" />
                            <x-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao', $turma->descricao)" required />
                            <x-input-error for="descricao" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="dt_inicio" value="Data de Início" />
                                <x-input id="dt_inicio" class="block mt-1 w-full" type="date" name="dt_inicio" :value="old('dt_inicio', $turma->dt_inicio?->format('Y-m-d'))" required />
                                <x-input-error for="dt_inicio" class="mt-1" />
                            </div>
                            <div>
                                <x-label for="dt_fim" value="Data de Término" />
                                <x-input id="dt_fim" class="block mt-1 w-full" type="date" name="dt_fim" :value="old('dt_fim', $turma->dt_fim?->format('Y-m-d'))" required />
                                <x-input-error for="dt_fim" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('turmas.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Salvar</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
