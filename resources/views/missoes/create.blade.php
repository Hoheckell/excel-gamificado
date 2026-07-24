<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Nova Missão</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Cadastrar Missão</h3>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('missoes.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-label for="titulo" value="Título da Missão" />
                            <x-input id="titulo" class="block mt-1 w-full" type="text" name="titulo" :value="old('titulo', 'Missão Prática')" required />
                            <x-input-error for="titulo" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="ordem" value="Ordem cronológica" />
                            <x-input id="ordem" class="block mt-1 w-full" type="number" name="ordem" :value="old('ordem', 1)" min="1" required />
                            <x-input-error for="ordem" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="descricao" value="Descrição da Missão" />
                            <textarea id="descricao" name="descricao" rows="4" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm" placeholder="Descreva o desafio da missão...">{{ old('descricao') }}</textarea>
                            <p class="text-xs text-[--text-muted] mt-1">HTML seguro é aceito (títulos, parágrafos, listas, tabelas, links e blocos de código).</p>
                            <x-input-error for="descricao" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="url" value="URL de apoio (opcional)" />
                            <x-input id="url" class="block mt-1 w-full" type="url" name="url" :value="old('url')" placeholder="https://exemplo.com/material" />
                            <x-input-error for="url" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="anexos" value="Anexos da missão (opcional)" />
                            <input id="anexos" name="anexos[]" type="file" multiple accept=".png,.jpg,.xls,.xlsx,.docs,.doc,.csv,.txt,.pdf" class="mt-1 block w-full text-sm text-[--text-main] file:mr-4 file:rounded file:border-0 file:bg-excel-tint file:px-3 file:py-2 file:text-excel-dark">
                            <p class="text-xs text-[--text-muted] mt-1">Sem limite de quantidade; até 3 MB por arquivo. Formatos: PNG, JPG, XLS, XLSX, DOCS, DOC, CSV, TXT e PDF.</p>
                            <x-input-error for="anexos" class="mt-1" />
                            <x-input-error for="anexos.*" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="pontuacao" value="Pontuação" />
                            <x-input id="pontuacao" class="block mt-1 w-full" type="number" name="pontuacao" :value="old('pontuacao', 100)" min="1" max="500" required />
                            <p class="text-xs text-[--text-muted] mt-1">Valor em pontos que a equipe recebe ao concluir (1 a 500).</p>
                            <x-input-error for="pontuacao" class="mt-1" />
                        </div>

                        <div class="space-y-3 rounded-excel border border-[--border-light] bg-[#f8faf8] p-4">
                            <p class="text-sm font-semibold text-[--text-main]">Entrega do aluno</p>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="permite_resposta" value="1" @checked(old('permite_resposta')) class="mt-0.5 rounded border-[--border-light] text-excel-dark focus:ring-excel-light">
                                <span>
                                    <span class="block text-sm text-[--text-main]">Permitir resposta textual</span>
                                    <span class="block text-xs text-[--text-muted]">O aluno poderá escrever uma resposta opcional ao concluir.</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="permite_anexo" value="1" @checked(old('permite_anexo')) class="mt-0.5 rounded border-[--border-light] text-excel-dark focus:ring-excel-light">
                                <span>
                                    <span class="block text-sm text-[--text-main]">Permitir anexo</span>
                                    <span class="block text-xs text-[--text-muted]">O aluno poderá enviar um arquivo opcional de até 10 MB.</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('missoes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Criar Missão</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
