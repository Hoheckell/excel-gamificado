<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Editar Badge: {{ $badge->nome }}</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Editar conquista</h3>
            </div>
            <div class="p-8">
                <form method="POST" action="{{ route('badges.update', $badge) }}">
                    @csrf
                    @method('PUT')
                    @include('badges._form', ['badge' => $badge])
                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('badges.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Salvar</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
