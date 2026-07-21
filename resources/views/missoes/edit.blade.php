<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Editar Missão</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="bg-white/20 text-white px-2.5 py-1 rounded text-xs font-mono font-bold tracking-wider">{{ $missao->pontuacao }} pts</span>
                    <span class="text-white/60 text-xs">Criada em {{ $missao->created_at->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('missoes.update', $missao) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <x-label for="titulo" value="Título da Missão" />
                            <x-input id="titulo" class="block mt-1 w-full" type="text" name="titulo" :value="old('titulo', $missao->titulo)" required />
                            <x-input-error for="titulo" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="ordem" value="Ordem cronológica" />
                            <x-input id="ordem" class="block mt-1 w-full" type="number" name="ordem" :value="old('ordem', $missao->ordem)" min="1" required />
                            <x-input-error for="ordem" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="descricao" value="Descrição da Missão" />
                            <textarea id="descricao" name="descricao" rows="4" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">{{ old('descricao', $missao->descricao) }}</textarea>
                            <x-input-error for="descricao" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="pontuacao" value="Pontuação" />
                            <x-input id="pontuacao" class="block mt-1 w-full" type="number" name="pontuacao" :value="old('pontuacao', $missao->pontuacao)" min="1" max="500" required />
                            <x-input-error for="pontuacao" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('missoes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Salvar</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
