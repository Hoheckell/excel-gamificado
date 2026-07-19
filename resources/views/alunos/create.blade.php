<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ auth()->user()->isProfessor() ? __('Cadastrar Aluno') : __('Editar Perfil') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto p-6">
        <div class="portal-container">
            <div class="bg-white border-b border-[--border-light] px-6 py-4">
                <p class="text-sm text-[--text-muted]">
                    {{ auth()->user()->isProfessor() ? 'Preencha os dados para cadastrar um novo aluno na turma.' : 'Atualize suas informações de perfil.' }}
                </p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('alunos.store') }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-label for="name" value="{{ __('Nome Completo') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error for="name" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="email" value="{{ __('E-mail') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error for="email" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="password" value="{{ __('Senha') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <x-input-error for="password" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="{{ __('Confirmar Senha') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        </div>

                        <div>
                            <x-label for="turma_id" value="{{ __('Turma') }}" />
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
                            <x-label for="equipe_id" value="{{ __('Equipe (opcional)') }}" />
                            <select id="equipe_id" name="equipe_id" class="mt-1 block w-full border border-[--border-light] rounded-excel px-3 py-2.5 text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light text-sm">
                                <option value="">Sem equipe</option>
                                @foreach ($equipes as $equipe)
                                    <option value="{{ $equipe->id }}" {{ old('equipe_id') == $equipe->id ? 'selected' : '' }}>
                                        {{ $equipe->nome }} (Turma: {{ $equipe->turma->codigo ?? '—' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="equipe_id" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('alunos.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">
                            Cancelar
                        </a>
                        <x-button>
                            {{ __('Cadastrar Aluno') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
