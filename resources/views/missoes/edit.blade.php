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
                <form method="POST" action="{{ route('missoes.update', $missao) }}" enctype="multipart/form-data">
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
                            <p class="text-xs text-[--text-muted] mt-1">HTML seguro é aceito (títulos, parágrafos, listas, tabelas, links e blocos de código).</p>
                            <x-input-error for="descricao" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="url" value="URL de apoio (opcional)" />
                            <x-input id="url" class="block mt-1 w-full" type="url" name="url" :value="old('url', $missao->url)" placeholder="https://exemplo.com/material" />
                            <x-input-error for="url" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="anexos" value="Adicionar anexos (opcional)" />
                            <input id="anexos" name="anexos[]" type="file" multiple accept=".png,.jpg,.xls,.xlsx,.docs,.doc,.csv,.txt,.pdf" class="mt-1 block w-full text-sm text-[--text-main] file:mr-4 file:rounded file:border-0 file:bg-excel-tint file:px-3 file:py-2 file:text-excel-dark">
                            <p class="text-xs text-[--text-muted] mt-1">Sem limite de quantidade; até 3 MB por arquivo.</p>
                            <x-input-error for="anexos" class="mt-1" />
                            <x-input-error for="anexos.*" class="mt-1" />

                            @if ($missao->anexos->isNotEmpty())
                                <div class="mt-3 space-y-2">
                                    <p class="text-xs font-semibold text-[--text-main]">Anexos atuais</p>
                                    @foreach ($missao->anexos as $anexo)
                                        @if ($anexo->removido_em)
                                            <p class="text-sm font-medium text-amber-700">{{ $anexo->nome_original }} — o arquivo não existe porque a turma foi concluída.</p>
                                        @else
                                            <label class="flex items-center gap-2 text-sm text-[--text-main]">
                                                <input type="checkbox" name="remover_anexos[]" value="{{ $anexo->id }}" class="rounded border-[--border-light] text-red-600">
                                                Remover {{ $anexo->nome_original }} ({{ Number::fileSize($anexo->tamanho) }})
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <x-label for="pontuacao" value="Pontuação" />
                            <x-input id="pontuacao" class="block mt-1 w-full" type="number" name="pontuacao" :value="old('pontuacao', $missao->pontuacao)" min="1" max="500" required />
                            <x-input-error for="pontuacao" class="mt-1" />
                        </div>

                        <div class="space-y-3 rounded-excel border border-[--border-light] bg-[#f8faf8] p-4">
                            <p class="text-sm font-semibold text-[--text-main]">Entrega do aluno</p>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="permite_resposta" value="1" @checked(old('permite_resposta', $missao->permite_resposta)) class="mt-0.5 rounded border-[--border-light] text-excel-dark focus:ring-excel-light">
                                <span>
                                    <span class="block text-sm text-[--text-main]">Permitir resposta textual</span>
                                    <span class="block text-xs text-[--text-muted]">O aluno poderá escrever uma resposta opcional ao concluir.</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="permite_anexo" value="1" @checked(old('permite_anexo', $missao->permite_anexo)) class="mt-0.5 rounded border-[--border-light] text-excel-dark focus:ring-excel-light">
                                <span>
                                    <span class="block text-sm text-[--text-main]">Permitir anexo</span>
                                    <span class="block text-xs text-[--text-muted]">O aluno poderá enviar um arquivo opcional de até 10 MB.</span>
                                </span>
                            </label>
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
