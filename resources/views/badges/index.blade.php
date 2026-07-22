<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Badges</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Conquistas e bônus das equipes</p>
            </div>
            @can('create', App\Models\Badge::class)
                <a href="{{ route('badges.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nova Badge
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto p-6">
        @if (session('success'))
            <div class="mb-6 px-5 py-3 bg-excel-tint border border-excel-light text-excel-dark rounded-excel text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($badges as $badge)
                <div class="portal-container p-6 flex items-start gap-4">
                    <div class="w-14 h-14 rounded-full bg-excel-tint flex items-center justify-center text-2xl shrink-0">
                        {{ $badge->icone }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-[--text-main]">{{ $badge->nome }}</h3>
                                <span class="text-xs font-mono font-semibold text-excel-dark">+{{ $badge->pontos_bonus }} XP</span>
                            </div>
                            @canany(['update', 'delete'], $badge)
                                <div class="flex items-center gap-2 shrink-0">
                                    @can('update', $badge)
                                        <a href="{{ route('badges.edit', $badge) }}" class="text-xs text-[--text-muted] hover:text-excel-dark transition font-medium">Editar</a>
                                    @endcan
                                    @can('delete', $badge)
                                        <form method="POST" action="{{ route('badges.destroy', $badge) }}" onsubmit="return confirm('Excluir a badge {{ $badge->nome }}? Ela também será removida das equipes que a receberam.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-600 transition font-medium">Excluir</button>
                                        </form>
                                    @endcan
                                </div>
                            @endcanany
                        </div>
                        <p class="text-sm text-[--text-muted] mt-2">{{ $badge->descricao }}</p>
                        <p class="text-[10px] uppercase tracking-wider text-[--text-muted] mt-3">
                            Concedida a {{ $badge->equipes_count }} {{ $badge->equipes_count === 1 ? 'equipe' : 'equipes' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 text-center py-16 text-[--text-muted]">
                    <p>Nenhuma badge cadastrada.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
