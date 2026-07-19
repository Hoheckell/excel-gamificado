<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased excel-grid-bg">
        <x-banner />

        <div class="min-h-screen">
            @livewire('navigation-menu')

            @php $user = auth()->user(); @endphp
            @if ($user && $user->isAluno() && !$user->hasActiveTurma())
                <div class="bg-yellow-50 border-b-2 border-yellow-400">
                    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-sm text-yellow-800">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-500 shrink-0">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <span>Você não está vinculado a nenhuma turma ativa. Suas ações estão limitadas até entrar em uma turma.</span>
                        </div>
                        <form method="POST" action="{{ route('turmas.entrar') }}" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="codigo" required placeholder="Código da turma"
                                class="border border-yellow-400 rounded-excel px-3 py-1.5 text-sm text-[--text-main] bg-white focus:border-excel-dark focus:ring-excel-light w-36 text-center uppercase"
                                maxlength="6" style="text-transform: uppercase;">
                            <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-excel bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                Entrar na Turma
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if (isset($header))
                <header class="excel-ribbon shadow-md">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        @stack('scripts')
    </body>
</html>
