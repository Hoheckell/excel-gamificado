<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ auth()->user()->isProfessor() ? 'Editar Aluno: ' . $aluno->name : 'Editar Meu Perfil' }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-6">
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-[38px] h-[38px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($aluno->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-[--text-main]">{{ $aluno->name }}</p>
                        <p class="text-xs text-[--text-muted]">
                            @if ($aluno->equipe)
                                Equipe: {{ $aluno->equipe->nome }}
                            @else
                                Sem equipe
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('alunos.update', $aluno) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div>
                            <x-label for="name" value="{{ __('Nome Completo') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $aluno->name)" required autofocus />
                            <x-input-error for="name" class="mt-1" />
                        </div>

                        @if (auth()->user()->isProfessor())
                            <div>
                                <x-label for="email" value="{{ __('E-mail') }}" />
                                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $aluno->email)" required />
                                <x-input-error for="email" class="mt-1" />
                            </div>

                            <div>
                                <x-label for="turma_id" value="{{ __('Turma') }}" />
                                <select id="turma_id" name="turma_id" required class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                    <option value="">Selecione</option>
                                    @foreach ($turmas as $turma)
                                        <option value="{{ $turma->id }}" {{ old('turma_id', $aluno->turmas->first()?->id) == $turma->id ? 'selected' : '' }}>
                                            {{ $turma->codigo }} - {{ $turma->descricao }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error for="turma_id" class="mt-1" />
                            </div>

                            <div>
                                <x-label for="equipe_id" value="{{ __('Equipe') }}" />
                                <select id="equipe_id" name="equipe_id" class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                    <option value="">Sem equipe</option>
                                    @foreach ($equipes as $equipe)
                                        <option value="{{ $equipe->id }}" {{ old('equipe_id', $aluno->equipe_id) == $equipe->id ? 'selected' : '' }}>
                                            {{ $equipe->nome }} (Turma: {{ $equipe->turma->codigo ?? '—' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error for="equipe_id" class="mt-1" />
                            </div>

                            <div>
                                <label class="flex items-start gap-2 cursor-pointer">
                                    <input type="checkbox" name="autorizado" value="1" {{ old('autorizado', $aluno->autorizado) ? 'checked' : '' }} class="mt-0.5 rounded border-gray-300 text-excel-dark focus:ring-excel-light">
                                    <div>
                                        <span class="text-sm font-medium text-[--text-main]">Autorizar Emissão de Certificado</span>
                                        <p class="text-xs text-[--text-muted] mt-0.5">Permite que o aluno acesse a tela de emissão de certificado.</p>
                                    </div>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('alunos.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">
                            Cancelar
                        </a>
                        <x-button>
                            {{ __('Salvar Alterações') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
