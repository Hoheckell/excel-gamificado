<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Nova Badge</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div class="portal-container">
            <div class="excel-ribbon px-6 py-4">
                <h3 class="text-white font-semibold">Cadastrar conquista</h3>
            </div>
            <div class="p-8">
                <form method="POST" action="{{ route('badges.store') }}">
                    @csrf
                    @include('badges._form', ['badge' => null])
                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('badges.index') }}" class="text-sm text-[--text-muted] hover:text-excel-dark transition">Cancelar</a>
                        <x-button>Criar Badge</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
