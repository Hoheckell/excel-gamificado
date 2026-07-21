<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">{{ $missao->ordem }}. {{ $missao->titulo }} — {{ $missao->pontuacao }} pts</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-white/60 uppercase tracking-wider">Missão #{{ $missao->id }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white">{{ $missao->pontuacao }} pts</span>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6 p-4 rounded-excel bg-[#f8faf8]">
                    <p class="text-[--text-main] leading-relaxed">{{ $missao->descricao }}</p>
                </div>

                <h3 class="font-semibold text-sm text-[--text-main] mb-3">Equipes vinculadas ({{ $missao->equipes->count() }})</h3>
                @if ($missao->equipes->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($missao->equipes as $equipe)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-excel-tint text-excel-dark">
                                {{ $equipe->nome }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-[--text-muted] italic">Nenhuma equipe vinculada.</p>
                @endif

                <div class="flex justify-end mt-6 gap-3">
                    <a href="{{ route('missoes.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Voltar</a>
                    @can('update', $missao)
                        <a href="{{ route('missoes.edit', $missao) }}" class="btn-excel inline-block text-sm px-4 py-2">Editar</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
