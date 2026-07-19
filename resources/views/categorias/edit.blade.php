<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Editar Categoria: {{ $categoria->nome }}</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Editar Categoria de Classificação</h3>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('categorias.update', $categoria) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <x-label for="nome" value="Nome da Categoria" />
                            <x-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $categoria->nome)" required autofocus />
                            <x-input-error for="nome" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="pt_classificacao" value="Pontuação Mínima" />
                            <x-input id="pt_classificacao" class="block mt-1 w-full" type="number" name="pt_classificacao" :value="old('pt_classificacao', $categoria->pt_classificacao)" min="0" required />
                            <x-input-error for="pt_classificacao" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="descricao" value="Descrição (opcional)" />
                            <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">{{ old('descricao', $categoria->descricao) }}</textarea>
                            <x-input-error for="descricao" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="titulo_certificado" value="Título no Certificado (opcional)" />
                            <x-input id="titulo_certificado" class="block mt-1 w-full" type="text" name="titulo_certificado" :value="old('titulo_certificado', $categoria->titulo_certificado)" />
                            <x-input-error for="titulo_certificado" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="cor" value="Cor de Destaque" />
                            <div class="flex items-center gap-2 mt-1">
                                <x-input id="cor" class="block w-full" type="color" name="cor" :value="old('cor', $categoria->cor)" />
                            </div>
                            <x-input-error for="cor" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('categorias.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Salvar</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
