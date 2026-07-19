<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Nova Turma</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Dados da Turma</h3>
                <p class="text-xs text-white/60 mt-0.5">O código será gerado automaticamente ao salvar.</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('turmas.store') }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-label for="descricao" value="Descrição da Turma" />
                            <x-input id="descricao" class="block mt-1 w-full" type="text" name="descricao" :value="old('descricao')" placeholder="Ex: Excel Básico — Turma Manhã" required autofocus />
                            <x-input-error for="descricao" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="dt_inicio" value="Data de Início" />
                                <x-input id="dt_inicio" class="block mt-1 w-full" type="date" name="dt_inicio" :value="old('dt_inicio')" required />
                                <x-input-error for="dt_inicio" class="mt-1" />
                            </div>
                            <div>
                                <x-label for="dt_fim" value="Data de Término" />
                                <x-input id="dt_fim" class="block mt-1 w-full" type="date" name="dt_fim" :value="old('dt_fim')" required />
                                <x-input-error for="dt_fim" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('turmas.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Criar Turma</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
