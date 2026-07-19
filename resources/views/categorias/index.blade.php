<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">Categorias de Classificação</h2>
                <p class="text-xs text-white/70 mt-0.5 uppercase tracking-wider">Patamares de conquista por pontuação</p>
            </div>
            @can('create', App\Models\Categoria::class)
                <a href="{{ route('categorias.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-excel text-sm font-semibold transition">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nova Categoria
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

        <div class="space-y-5">
            @forelse ($categorias as $categoria)
                <div class="portal-container border-t-4" style="border-top-color: {{ $categoria->cor }}">
                    <div class="px-6 py-5 flex flex-wrap items-start justify-between gap-4" style="background-color: {{ $categoria->cor }}15">
                        <div class="flex items-start gap-4">
                            <div class="w-[48px] h-[48px] rounded-full flex items-center justify-center font-bold text-white text-lg shrink-0" style="background-color: {{ $categoria->cor }}">
                                @if ($categoria->pt_classificacao >= 380)
                                    {{ $loop->iteration }}
                                @else
                                    &#9733;
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-[--text-main]">{{ $categoria->nome }}</h3>
                                <span class="text-sm font-mono font-semibold" style="color: {{ $categoria->cor }}">
                                    {{ $categoria->pt_classificacao }} a {{ $categoria->pt_limite_superior }} pontos
                                </span>
                                @if ($categoria->descricao)
                                    <p class="text-sm text-[--text-main] leading-relaxed mt-2 max-w-2xl">
                                        {{ $categoria->descricao }}
                                    </p>
                                @endif
                                @if ($categoria->titulo_certificado)
                                    <div class="mt-3 p-3 rounded-excel border" style="background-color: {{ $categoria->cor }}10; border-color: {{ $categoria->cor }}40">
                                        <span class="text-xs font-semibold uppercase tracking-wider" style="color: {{ $categoria->cor }}">Título no Certificado</span>
                                        <p class="text-sm mt-0.5" style="color: {{ $categoria->cor }}dd"><em>{{ $categoria->titulo_certificado }}</em></p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @canany(['update', 'delete'], $categoria)
                            <div class="flex items-center gap-2 shrink-0">
                                @can('update', $categoria)
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="text-xs text-[--text-muted] hover:text-excel-dark transition font-medium px-3 py-1.5 border border-[--border-light] rounded-excel">
                                        Editar
                                    </a>
                                @endcan
                                @can('delete', $categoria)
                                    <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" onsubmit="return confirm('Excluir categoria {{ $categoria->nome }}?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-600 transition font-medium px-3 py-1.5 border border-red-200 rounded-excel">
                                            Excluir
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endcanany
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-[--text-muted]">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="mx-auto mb-3 text-[--excel-grid]">
                        <path d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z"/>
                    </svg>
                    <p>Nenhuma categoria cadastrada.</p>
                </div>
            @endforelse
        </div>

        {{-- Explicação de como as categorias se vinculam às equipes --}}
        @if ($categorias->isNotEmpty())
            <div class="portal-container mt-8">
                <div class="excel-ribbon px-6 py-4">
                    <h3 class="text-white font-semibold">Como as equipes são classificadas</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-[--text-main] leading-relaxed">
                        Cada equipe acumula pontos ao completar missões. A <strong class="text-excel-dark">categoria</strong> da equipe é determinada automaticamente comparando sua pontuação total com as faixas acima.
                    </p>
                    <ul class="mt-3 space-y-2 text-sm text-[--text-muted]">
                        <li class="flex items-start gap-2">
                            <span class="text-excel-dark mt-0.5">&#8226;</span>
                            A equipe com <strong>maior pontuação</strong> alcança a categoria mais alta (menor valor de <em>pt_classificacao</em> compatível).
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-excel-dark mt-0.5">&#8226;</span>
                            A categoria define o <strong>título no certificado</strong> de cada integrante da equipe.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-excel-dark mt-0.5">&#8226;</span>
                            Professores podem ajustar as faixas de pontuação a qualquer momento.
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
