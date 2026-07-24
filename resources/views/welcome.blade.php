<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Excel Workshop') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
        @endif
    </head>
    <body class="font-sans antialiased excel-grid-bg min-h-screen flex flex-col items-center justify-center p-5">
        <header class="w-full max-w-lg text-sm mb-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 text-white bg-excel-dark rounded-excel text-sm font-medium hover:bg-excel-light transition">
                            Painel
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 text-white bg-excel-dark rounded-excel text-sm font-medium hover:bg-excel-light transition">
                                Registrar
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <div class="portal-container w-full max-w-lg">
            <div class="excel-ribbon h-3 w-full"></div>

            <div class="text-center flex flex-col items-center px-10 py-12">
                <div class="icon-wrapper mb-6 w-[70px] h-[70px] rounded-xl border-2 border-dashed border-excel-light" style="border-radius: 12px;">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/>
                    </svg>
                </div>

                <span class="text-sm uppercase tracking-[3px] text-excel-light font-bold mb-1">Sistema Pedagógico</span>
                <h1 class="text-[28px] text-excel-dark font-bold leading-tight mb-2">Curso de Excel</h1>

                <div class="professor-badge mb-5">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5-4-8-4z"/>
                    </svg>
                    <span>Professor: Hoheckell Nunes</span>
                </div>

                <a href="{{ route('regras') }}" class="inline-block text-sm text-excel-dark hover:text-excel-light font-semibold mb-5 underline underline-offset-4">
                    Conheça o Sistema
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-excel w-full text-center inline-block">
                            Entrar
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-excel w-full text-center inline-block">
                            Entrar
                        </a>
                    @endauth
                @endif
            </div>

            <div class="sheet-tabs-bar">
                <div class="excel-tab-active">Painel</div>
                <div class="excel-tab-inactive">Módulos</div>
                <div class="excel-tab-inactive">Minhas Notas</div>
                <div class="excel-tab-inactive border-r-0">Suporte</div>
            </div>
        </div>
    </body>
</html>
