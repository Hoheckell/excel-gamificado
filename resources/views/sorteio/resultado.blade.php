<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Resultado do Sorteio</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">
                    Turma: {{ $turma->codigo }} — {{ $equipesSorteadas->count() }} equipes de ~{{ $integrantes }} alunos
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6">
        <form method="POST" action="{{ route('sorteio.concluir') }}">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $turma->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($equipesSorteadas as $index => $alunos)
                    <div class="portal-container">
                        <div class="excel-ribbon px-5 py-3 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold text-sm">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <input type="text"
                                       name="equipes[{{ $index }}][nome]"
                                       value="{{ old("equipes.{$index}.nome") }}"
                                       placeholder="Nome da Equipe {{ $index + 1 }}"
                                       required
                                       class="w-full bg-white/10 border border-white/30 rounded-excel px-3 py-1.5 text-white placeholder-white/50 text-sm focus:outline-none focus:bg-white/20 focus:border-white/50 transition">
                            </div>
                        </div>

                        <div class="p-5">
                            <span class="text-xs text-[--text-muted] uppercase tracking-wider font-semibold mb-3 block">{{ $alunos->count() }} integrantes</span>
                            <div class="space-y-2">
                                @foreach ($alunos as $aluno)
                                    <input type="hidden" name="equipes[{{ $index }}][alunos][]" value="{{ $aluno->id }}">
                                    <div class="flex items-center gap-3 p-2.5 rounded-excel bg-[#f8faf8]">
                                        <div class="w-[32px] h-[32px] rounded-full bg-excel-tint text-excel-dark flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($aluno->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-medium text-[--text-main]">{{ $aluno->name }}</span>
                                        @if ($aluno->equipe_id)
                                            <span class="text-[10px] text-orange-500 ml-auto">já em equipe</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-between items-center">
                <div class="flex gap-3">
                    <a href="{{ route('sorteio.create') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">
                        Voltar e Sortear Novamente
                    </a>
                    <button form="resort-form" type="submit" class="text-sm text-excel-dark hover:text-excel-light transition font-medium">
                        Redistribuir
                    </button>
                </div>
                <x-button class="px-8 py-3">
                    Concluir — Criar {{ $equipesSorteadas->count() }} Equipes
                </x-button>
            </div>
        </form>

        {{-- Form oculto para re-sortear --}}
        <form id="resort-form" method="POST" action="{{ route('sorteio.sortear') }}" class="hidden">
            @csrf
            <input type="hidden" name="turma_id" value="{{ $turma->id }}">
            <input type="hidden" name="integrantes" value="{{ $integrantes }}">
        </form>
    </div>
</x-app-layout>
